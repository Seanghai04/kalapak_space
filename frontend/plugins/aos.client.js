import AOS from "aos";

export default defineNuxtPlugin(() => {
  requestAnimationFrame(() => {
    AOS.init({
      duration: 800,
      easing: "ease-out-cubic",
      once: true,
      offset: 50,
      startEvent: "DOMContentLoaded",
      initClassName: false,
      useClassNames: false,
    });
  });
});
