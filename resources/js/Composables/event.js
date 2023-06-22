import { onMounted, onBeforeUnmount } from 'vue';

export function useEventListener(target, event, callback) {
    onMounted(() => target.addEventListener(event, callback));
    onBeforeUnmount(() => target.removeEventListener(event, callback));
}