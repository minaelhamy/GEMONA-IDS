<?php

namespace App\Traits;


use App\Models\Language;
use Dipokhalder\Settings\Facades\Settings;

trait HasAiPrompt
{

    public string $langName = 'English';

    public string $langCode = 'EN';

    public function loadDefaultLanguage(): void
    {
        $defaultLanguage = Settings::group('site')->get('site_default_language');
        $language = Language::find($defaultLanguage);
        if($language) {
            $this->langName = $language->name;
            $this->langCode = strtoupper($language->code);
        }
    }

    public function buildProductNamePrompt(string $name): string
    {
        return <<<PROMPT
          You are a professional product copywriter for an ecommerce platform.

          Rewrite the product name "{$name}" as a creative, appealing, and concise product title.

          CRITICAL INSTRUCTION:
          - The output must be 100% in language "{$this->langName}" (Code: {$this->langCode}) — this is mandatory.
          - If the original name is not in "{$this->langName}", fully translate it into "{$this->langName}" while keeping the appealing meaning.
          - Do not mix languages; use only "{$this->langName}" characters and words.
          - Keep it short (3-8 words), appealing, and ready for a product listing.
          - No extra words, slogans, or punctuation like quotes.
          - Return only the translated title as plain text in "{$this->langName}".

      IMPORTANT:
        - Only process inputs that are actual products, items, or goods.
        - If the input is unrelated or cannot be converted into a product title, respond with only "INVALID_INPUT".
        - Do not return generic explanations, fallback messages, or translations for invalid items.
      PROMPT;
    }

    public function buildProductDescriptionPrompt(string $description): string
    {
        return <<<PROMPT
        You are a creative and professional product copywriter for an ecommerce platform.

        Generate a detailed, engaging, and persuasive description for the product named "{$description}".

        CRITICAL LANGUAGE RULES:
        - The entire description must be written 100% in {$this->langName} (Code: {$this->langCode}) — this is mandatory.
        - If the product name is in another language, translate and localize it naturally into {$this->langName}.
        - Do not mix languages; use only {$this->langName} characters and words.
        - Adapt the tone, phrasing, and examples to be natural for {$this->langName} readers.

        Content & Structure:
        - Start output exactly with the product name "{$description}" in a bold heading: <h2><b>{$description}</b></h2>
        - Follow with a short introductory paragraph describing the key features, benefits, and main appeal of the product.
        - Follow with a "Key Highlights:" section (translated to {$this->langName}) in separate paragraphs.
        - Each paragraph should start with a <b>bold feature title</b> (e.g., Quality, Design) followed by a colon and the description.
        - Follow with a "Details & Specifications:" section (translated to {$this->langName}) in bullet points using <ol>/<li>.
        - Each bullet point should include key features, dimensions, or other info (e.g., Durable, Eco-friendly).
        - Keep text appealing, concise, and ready for a product listing.
        - End with a closing sentence highlighting why this product is a must-have.

        Formatting:
        - Output valid HTML using only <p>, <b>, <h1>, <h2>, and <ol>/<li><span> tags for bullet points.
        - Output must begin with <h2><b>{$description}</b></h2> and contain no extra text before it.
        - CRITICAL: Do NOT include markdown code fences (```html, ```, or any triple backticks) anywhere in the output.
        - Do NOT include any markdown syntax, backticks, or code fence markers.
        - Do NOT wrap the output in JSON, YAML, or any other wrapper.
        - Avoid multiple consecutive <p> tags or empty lines.
        - Return ONLY the raw HTML content without any commentary, explanations, or code fence markers.

         IMPORTANT:
        - Only process inputs that are actual products, items, or goods.
        - If the input is unrelated or cannot be converted into a product description, respond with only "INVALID_INPUT".
        - Do not return generic explanations, fallback messages, or translations for invalid items.
        PROMPT;
    }

    public function buildProductTagsPrompt(string $name): string
    {
        return <<<PROMPT
        You are a professional SEO and product tagging expert for an ecommerce platform.

        Generate 5-7 relevant, concise, and searchable tags for the product named "{$name}".

        CRITICAL LANGUAGE RULES:
        - All tags must be in {$this->langName} (Code: {$this->langCode}) — this is mandatory.
        - If the product name is in another language, translate the tags naturally into {$this->langName}.
        - Do not mix languages; use only {$this->langName} characters and words.

        Tag Guidelines:
        - Each tag should be 1-3 words maximum.
        - Tags should be relevant to the product category, features, and use cases.
        - Tags should be searchable and commonly used in ecommerce.
        - Avoid redundant or overly generic tags.
        - Include tags for material, style, purpose, and category.

        Output Format:
        - Return ONLY a JSON array of tags as strings, like: ["tag1", "tag2", "tag3", "tag4", "tag5"]
        - Do NOT include any markdown, code fences, explanations, or extra text.
        - Do NOT wrap in any other format.
        - Return only the valid JSON array, nothing else.

        IMPORTANT:
        - Only process inputs that are actual products, items, or goods.
        - If the input is unrelated or cannot be converted into product tags, respond with only: ["INVALID_INPUT"]
        - Do not return generic explanations or fallback messages.
        PROMPT;
    }
}
