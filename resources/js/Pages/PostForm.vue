<template>
  <v-card class="pa-4 mb-4">
    <v-card-title>{{ post?.isNew ? 'Create Post' : 'Edit Post' }}</v-card-title>
    <v-card-text>
      <v-text-field v-model="title" label="Title" />
      <v-textarea v-model="content" label="Content" rows="6" />
      <v-select v-model="status" :items="['draft','publish']" label="Status" />
    </v-card-text>
    <v-card-actions>
      <v-btn color="primary" @click="save">Save</v-btn>
      <v-btn @click="close">Cancel</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import { defineProps, defineEmits } from 'vue';

const props = defineProps({ post: Object });
const emit = defineEmits(['saved', 'close']);

const title = ref('');
const content = ref('');
const status = ref('draft');

watch(() => props.post, (val) => {
  if (val) {
    title.value = val.title?.rendered || val.title || '';
    content.value = val.content?.rendered || val.content || '';
    status.value = val.status || 'draft';
  }
}, { immediate: true });

async function save() {
  const data = { title: title.value, content: content.value, status: status.value };

  if (props.post?.isNew) {
    await axios.post('/back-office/api/posts', data);
  } else {
    await axios.put(`/back-office/api/posts/${props.post.id}`, data);
  }

  emit('saved');
  emit('close');
}

function close() {
  emit('close');
}
</script>
