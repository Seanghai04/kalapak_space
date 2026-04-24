import AOS from "aos";

export default defineNuxtPlugin(() => {
  AOS.init({
    duration: 800,
    easing: "ease-out-cubic",
    once: true,
    offset: 50,
  });
});
