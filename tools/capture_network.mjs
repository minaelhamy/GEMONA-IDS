import fs from "node:fs";
import path from "node:path";
import { chromium } from "playwright";

const sources = {
  hyperone: "https://www.hyperone.com.eg/en",
  carrefour: "https://www.carrefouregypt.com/mafegy/en/",
};

const source = process.argv[2];
const startUrl = sources[source] || process.argv[2];
const explicitOutput = process.argv[3];
if (!startUrl) {
  console.error("Usage: npm run capture:network -- <source|url> [output-name]");
  process.exit(2);
}

const outputName = (explicitOutput || source || "custom").replace(/[^a-z0-9_-]+/gi, "_");
const outputDir = path.join("data", "discovery", outputName);
fs.mkdirSync(outputDir, { recursive: true });
const jsonlPath = path.join(outputDir, "network.jsonl");
const screenshotPath = path.join(outputDir, "page.png");
const htmlPath = path.join(outputDir, "page.html");
fs.writeFileSync(jsonlPath, "");

const browser = await chromium.launch({
  headless: true,
  args: [
    "--disable-http2",
    "--disable-features=IsolateOrigins,site-per-process",
  ],
});
const context = await browser.newContext({
  locale: "en-US",
  timezoneId: "Africa/Cairo",
  geolocation: { latitude: 30.025835474117635, longitude: 31.483560809312824 },
  permissions: ["geolocation"],
  viewport: { width: 1440, height: 1100 },
  userAgent:
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0 Safari/537.36",
});
const page = await context.newPage();

page.on("response", async (response) => {
  const request = response.request();
  const resourceType = request.resourceType();
  const url = response.url();
  const contentType = response.headers()["content-type"] || "";
  if (!["xhr", "fetch"].includes(resourceType) && !contentType.includes("json")) {
    return;
  }
  let body = null;
  try {
    const text = await response.text();
    body = text.slice(0, 250000);
  } catch {
    body = null;
  }
  const record = {
    url,
    status: response.status(),
    method: request.method(),
    resourceType,
    contentType,
    postData: request.postData(),
    body,
  };
  fs.appendFileSync(jsonlPath, `${JSON.stringify(record)}\n`);
});

try {
  await page.goto(startUrl, { waitUntil: "commit", timeout: 45000 });
  await page.waitForTimeout(12000);

  const selectors = [
    'button:has-text("Accept")',
    'button:has-text("Allow")',
    'button:has-text("Continue")',
    'button:has-text("Confirm")',
    'button:has-text("Use current location")',
    'text=Maadi',
    'text=Sheikh Zayed',
  ];
  for (const selector of selectors) {
    try {
      const locator = page.locator(selector).first();
      if (await locator.isVisible({ timeout: 1500 })) {
        await locator.click({ timeout: 3000 });
        await page.waitForTimeout(4000);
      }
    } catch {
      // Optional gate selectors vary per site.
    }
  }

  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight / 2));
  await page.waitForTimeout(5000);
  await page.screenshot({ path: screenshotPath, fullPage: true });
  fs.writeFileSync(htmlPath, await page.content());
  console.log(`Captured ${startUrl}`);
  console.log(`Network: ${jsonlPath}`);
  console.log(`Screenshot: ${screenshotPath}`);
} finally {
  await browser.close();
}
