/* Top bar scrolling start */
document.addEventListener('DOMContentLoaded', () => {
    const dbMain = document.querySelector('.db-main');
    const subHeader = document.querySelector('.sub-header');
    const dbHeader = document.querySelector('.db-header');

    if (dbMain) {
        let headerSize = dbMain.offsetWidth;
        if (headerSize < 736) {
            if (subHeader) {
                subHeader.style.top = `${dbHeader.scrollHeight}px`;
            }

            dbMain.addEventListener('scroll', () => {
                let scroll = dbMain.scrollTop;
                if (scroll === 0) {
                    subHeader.style.display = 'block';
                } else {
                    subHeader.style.display = 'none';
                }
            });
        }
    }
});


/* Variation start */

document.addEventListener('click', function (event) {
    if (event.target.matches('.size-tabs label')) {
        document.querySelectorAll('.size-tabs label').forEach((label) => {
            label.classList.remove('active');
        });
        event.target.classList.add('active');
    }
});

document.addEventListener('click', function (event) {
    if (event.target.closest('.extra-swiper .extra')) {
        const extra = event.target.closest('.extra-swiper .extra');
        const input = extra.querySelector('input');
        if (input) {
            input.checked = !input.checked;
            extra.classList.toggle('active', input.checked);
        }
    }
});

document.addEventListener('click', function (event) {
    if (event.target.closest('.addon-swiper .addon')) {
        const addon = event.target.closest('.addon-swiper .addon');
        addon.classList.toggle('active');
    }
});
/* Variation end */

/* Other button tab stat */
document.querySelectorAll('.other-tabBtn').forEach(function (tabBtn) {
    tabBtn.addEventListener('click', function () {
        document.querySelectorAll('.other-tabBtn').forEach(function (btn) {
            btn.classList.remove('active');
            const tab = btn.getAttribute('data-tab');
            if (tab) {
                const tabElement = document.querySelector(tab);
                if (tabElement) {
                    tabElement.classList.remove('active');
                }
            }
        });

        this.classList.add('active');
        const dataTab = this.getAttribute('data-tab');
        if (dataTab) {
            const tabElement = document.querySelector(dataTab);
            if (tabElement) {
                tabElement.classList.add('active');
            }
        }
    });
});
/* Other button tab end */

document.addEventListener('click', function (event) {
    if (event.target.classList.contains('db-tab-btn')) {
        document.querySelectorAll('.db-tab-btn').forEach((btn) => {
            btn.classList.remove('active');
            const tabSelector = btn.getAttribute('data-tab');
            if (tabSelector) {
                const tab = document.querySelector(tabSelector);
                if (tab) {
                    tab.classList.remove('active');
                }
            }
        });
        event.target.classList.add('active');
        const dataTab = event.target.getAttribute('data-tab');
        if (dataTab) {
            const tab = document.querySelector(dataTab);
            if (tab) {
                tab.classList.add('active');
            }
        }
    }
});
/* db tab button end */

/* POS responsive start */

document.querySelectorAll('.db-pos-cartBtn').forEach(function (cartBtn) {
    cartBtn.addEventListener('click', function () {
        const cartDiv = document.querySelector('.db-pos-cartDiv');
        if (!cartDiv.classList.contains('active')) {
            cartDiv.classList.add('active');
        } else {
            cartDiv.classList.remove('active');
        }
    });
});

document.querySelectorAll('.db-pos-cartCls').forEach(function (cartCls) {
    cartCls.addEventListener('click', function () {
        document.querySelector('.db-pos-cartDiv').classList.remove('active');
    });
});

/* POS responsive close */


/* Message code start */
document.addEventListener('click', function (event) {
    if (event.target.classList.contains('close-the-image-file')) {
        event.target.parentElement.remove();
        document.querySelector('.chat-footer-data-list')?.classList.add('hidden');
    }
});

/* Message code end */


/* Installer menu active code start */
const handleLinkForInstaller = (param) => {
    window.location.replace(param);
};

document.querySelectorAll('.close-alert-button').forEach(function (button) {
    button.addEventListener('click', function () {
        this.parentElement.remove();
    });
});

/* Installer menu active code close */


/* Setting menu code start */

document.addEventListener('click', function (e) {
    const dropdownButton = e.target.closest('.dropdown-btn');

    if (dropdownButton) {
        e.stopPropagation();

        const dropdownGroup = dropdownButton.parentElement;
        const dropdownList = dropdownGroup.querySelector('.dropdown-list');

        if (!dropdownList.classList.contains('active')) {
            const allDropdownLists = document.querySelectorAll('.dropdown-list');
            allDropdownLists.forEach(list => list.classList.remove('active'));
        }

        dropdownList.classList.toggle('active');
    } else {
        document.querySelectorAll('.dropdown-list').forEach(function (list) {
            list.classList.remove('active');
        });
    }
});

/* Setting menu code end */

/* AI box dragging start */

document.addEventListener('DOMContentLoaded', function () {
  const initAiBoxDragging = () => {
    const aibox = document.querySelector('.ai-move');
    if (!aibox) {
      return;
    }

    aibox.style.position = 'fixed';
    aibox.style.zIndex = '40';
    aibox.style.cursor = 'grab';
    aibox.style.userSelect = 'none';
    aibox.style.right = '20px';
    aibox.style.bottom = '20px';

    let isDragging = false;
    let hasDragged = false;
    let offsetX = 0;
    let offsetY = 0;
    let startX = 0;
    let startY = 0;
    const dragThreshold = 5;

    const getClientCoords = (e) => {
      if (e.touches && e.touches[0]) {
        return { x: e.touches[0].clientX, y: e.touches[0].clientY };
      }
      return { x: e.clientX, y: e.clientY };
    };

    // Mouse events
    aibox.addEventListener('mousedown', (e) => {
      isDragging = true;
      hasDragged = false;
      const coords = getClientCoords(e);
      startX = coords.x;
      startY = coords.y;
      aibox.style.cursor = 'grabbing';
      offsetX = coords.x - aibox.getBoundingClientRect().left;
      offsetY = coords.y - aibox.getBoundingClientRect().top;
    });

    // Touch events
    aibox.addEventListener('touchstart', (e) => {
      isDragging = true;
      hasDragged = false;
      const coords = getClientCoords(e);
      startX = coords.x;
      startY = coords.y;
      offsetX = coords.x - aibox.getBoundingClientRect().left;
      offsetY = coords.y - aibox.getBoundingClientRect().top;
    }, { passive: true });

    document.addEventListener('mousemove', (e) => {
      if (!isDragging) return;

      const coords = getClientCoords(e);
      const dx = Math.abs(coords.x - startX);
      const dy = Math.abs(coords.y - startY);
      if (!hasDragged && (dx > dragThreshold || dy > dragThreshold)) {
        hasDragged = true;
      }

      const maxX = window.innerWidth - aibox.offsetWidth;
      const maxY = window.innerHeight - aibox.offsetHeight;
      let newX = coords.x - offsetX;
      let newY = coords.y - offsetY;

      newX = Math.max(0, Math.min(newX, maxX));
      newY = Math.max(0, Math.min(newY, maxY));

      aibox.style.left = `${newX}px`;
      aibox.style.top = `${newY}px`;
      aibox.style.right = 'auto';
      aibox.style.bottom = 'auto';
    });

    document.addEventListener('touchmove', (e) => {
      if (!isDragging) return;

      const coords = getClientCoords(e);
      const dx = Math.abs(coords.x - startX);
      const dy = Math.abs(coords.y - startY);
      if (!hasDragged && (dx > dragThreshold || dy > dragThreshold)) {
        hasDragged = true;
      }

      if (hasDragged) {
        e.preventDefault();
      }

      const maxX = window.innerWidth - aibox.offsetWidth;
      const maxY = window.innerHeight - aibox.offsetHeight;
      let newX = coords.x - offsetX;
      let newY = coords.y - offsetY;

      newX = Math.max(0, Math.min(newX, maxX));
      newY = Math.max(0, Math.min(newY, maxY));

      aibox.style.left = `${newX}px`;
      aibox.style.top = `${newY}px`;
      aibox.style.right = 'auto';
      aibox.style.bottom = 'auto';
    }, { passive: false });

    document.addEventListener('mouseup', () => {
      if (!isDragging) return;
      isDragging = false;
      aibox.style.cursor = 'grab';
      setTimeout(() => {
        hasDragged = false;
      }, 0);
    });

    document.addEventListener('touchend', () => {
      if (!isDragging) return;
      isDragging = false;
      setTimeout(() => {
        hasDragged = false;
      }, 0);
    });

    aibox.addEventListener('click', (e) => {
      if (hasDragged) {
        e.stopImmediatePropagation();
        e.preventDefault();
      }
    });
  };

  if (document.querySelector('.ai-move')) {
    initAiBoxDragging();
  } else {
    const observer = new MutationObserver(() => {
      if (document.querySelector('.ai-move')) {
        initAiBoxDragging();
        observer.disconnect();
      }
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }
});
/* AI box dragging end */
