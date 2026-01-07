<template>
  this is just a test - in Dashboard
  <div class="grid auto-rows-min gap-4 md:grid-cols-3">
    <div class="bg-muted/50 aspect-video rounded-xl" />
    <div class="bg-muted/50 aspect-video rounded-xl" />
    <div class="bg-muted/50 aspect-video rounded-xl" />
  </div>
  <div class="bg-muted/50 min-h-[100vh] flex-1 rounded-xl md:min-h-min" />
</template>

<script setup>
// import { Card, CardContent } from 'vue-shadcn'
</script>

<style scoped>
/* Optional: scrollbar styling for main content */
main::-webkit-scrollbar {
  width: 8px;
}
main::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.2);
  border-radius: 4px;
}
</style>
