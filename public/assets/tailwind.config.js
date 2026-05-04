/* Shared Tailwind config for Maharani Mobil UI */
window.tailwind = window.tailwind || {};
window.tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "primary": "#031636",
        "on-primary": "#ffffff",
        "primary-container": "#1a2b4c",
        "on-primary-container": "#8293ba",
        "primary-fixed": "#d8e2ff",
        "primary-fixed-dim": "#b6c6f0",
        "on-primary-fixed": "#071b3b",
        "on-primary-fixed-variant": "#364669",

        "secondary": "#835500",
        "secondary-container": "#feae2c",
        "secondary-fixed": "#ffddb4",
        "secondary-fixed-dim": "#ffb955",
        "on-secondary": "#ffffff",
        "on-secondary-container": "#6b4500",
        "on-secondary-fixed": "#291800",
        "on-secondary-fixed-variant": "#633f00",

        "tertiary": "#001d03",
        "tertiary-container": "#003408",
        "tertiary-fixed": "#a3f69c",
        "tertiary-fixed-dim": "#88d982",
        "on-tertiary": "#ffffff",
        "on-tertiary-container": "#55a454",
        "on-tertiary-fixed": "#002204",
        "on-tertiary-fixed-variant": "#005312",

        "background": "#f8f9fa",
        "surface": "#f8f9fa",
        "surface-bright": "#f8f9fa",
        "surface-dim": "#d9dadb",
        "surface-container": "#edeeef",
        "surface-container-low": "#f3f4f5",
        "surface-container-lowest": "#ffffff",
        "surface-container-high": "#e7e8e9",
        "surface-container-highest": "#e1e3e4",
        "surface-variant": "#e1e3e4",
        "surface-tint": "#4e5e82",

        "on-surface": "#191c1d",
        "on-surface-variant": "#44474e",
        "on-background": "#191c1d",
        "inverse-surface": "#2e3132",
        "inverse-on-surface": "#f0f1f2",
        "inverse-primary": "#b6c6f0",

        "outline": "#75777f",
        "outline-variant": "#c5c6cf",

        "error": "#ba1a1a",
        "error-container": "#ffdad6",
        "on-error": "#ffffff",
        "on-error-container": "#93000a"
      },
      borderRadius: {
        DEFAULT: "0.25rem",
        lg: "0.5rem",
        xl: "0.75rem",
        full: "9999px"
      },
      fontFamily: {
        headline: ["Plus Jakarta Sans"],
        body: ["Inter"],
        label: ["Inter"]
      }
    }
  }
};

