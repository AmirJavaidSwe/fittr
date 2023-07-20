import { computed, ref } from 'vue';
import { useEventListener } from './event';

export function useWindowSize() {
  const windowWidth = ref(window.innerWidth);
  const windowHeight = ref(window.innerHeight);
  const screen = computed(() => {
    switch (true) {
        case windowWidth.value >= 1536:
            return '2xl';
        case windowWidth.value >= 1280:
            return 'xl';
        case windowWidth.value >= 1024:
            return 'lg';
        case windowWidth.value >= 768:
            return 'md';
        case windowWidth.value >= 640:
            return 'sm';
    
        default:
            return 'mobile';
    }
  });

  useEventListener(window, 'resize', (event) => {
    windowWidth.value = event.target.innerWidth;
    windowHeight.value = event.target.innerHeight;
  });

  return { windowWidth, windowHeight, screen };
}