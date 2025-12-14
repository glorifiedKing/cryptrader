<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg
    width="40"
    height="42"
    viewBox="0 0 40 42"
    xmlns="http://www.w3.org/2000/svg"
>
    <!-- Background shape (optional) -->
    <rect
        x="1"
        y="1"
        width="38"
        height="40"
        rx="6"
        fill="#0F172A"
    />

    <!-- Initials -->
    <text
        x="20"
        y="27"
        text-anchor="middle"
        font-size="16"
        font-weight="700"
        font-family="Inter, Arial, sans-serif"
        fill="#FFFFFF"
        letter-spacing="1"
    >
        CT
    </text>
</svg>
</template>
