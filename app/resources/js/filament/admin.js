/**
 * Clipboard Copy Event Listener
 */
window.addEventListener("clipboard:copy", async (e) => {
  const text = e.detail?.text ?? "";
  if (!text) return;

  try {
    await navigator.clipboard.writeText(text);
  } catch {
    const ta = document.createElement("textarea");
    ta.value = text;
    ta.style.position = "fixed";
    ta.style.left = "-9999px";
    document.body.appendChild(ta);
    ta.select();
    document.execCommand("copy");
    ta.remove();
  }
});

document.addEventListener("DOMContentLoaded", () => {
  // Set the title attribute for file select inputs to show full filename on hover
  const fileSelectInputs = document.querySelectorAll(".fi-select-input");

  fileSelectInputs.forEach((input) => {
    input.addEventListener("mouseover", () => {
      const label = input.querySelector(".fi-select-input-value-label");

      if (label) {
        input.title = label.textContent;
      }
    });
  });

  // Auto open file picker on repeater item add
  document.addEventListener("click", (e) => {
    const addButton = e.target.closest(".fi-fo-repeater-add .fi-ac-btn-action");
    if (!addButton) return;

    const handler = () => {
      const repeaterItems = document.querySelectorAll(".fi-fo-repeater-item");

      if (!repeaterItems.length) return;

      const lastItem = repeaterItems[repeaterItems.length - 1];

      setTimeout(() => {
        const selectButton = lastItem.querySelector(".fi-select-input-btn");

        if (selectButton) {
          selectButton.click();
        }

        window.removeEventListener("sync-action-modals", handler);
      }, 100);
    };

    window.addEventListener("sync-action-modals", handler);
  });
});
