<template>
  <WordpressLayout>
    <v-container fluid class="pa-4">
      <!-- Header -->
      <v-row class="mb-6 align-center">
        <v-col>
          <h2 class="text-h5 font-weight-bold">Blog Posts</h2>
        </v-col>
        <v-col cols="auto" class="d-flex gap-3">
          <v-btn color="primary" @click="openCreate" elevation="2">
            <v-icon left>mdi-plus</v-icon>
            Create Post
          </v-btn>
          <v-btn color="secondary" @click="sync" elevation="2">
            <v-icon left>mdi-sync</v-icon>
            Sync from WP
          </v-btn>
        </v-col>
      </v-row>
   
      <v-card elevation="2" class="pa-4">
        <v-data-table
          :headers="headers"
          :items="posts"
          dense
          item-key="id"
        >
       
          <template #item.actions="{ item }">
            <v-btn icon @click="edit(item)" color="primary">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn icon @click="del(item)" color="red">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </template>
        </v-data-table>
      </v-card>

      <v-dialog v-model="dialog" max-width="600px">
        <v-card>
          <v-card-title>
            {{ editing?.isNew ? 'Create Post' : 'Edit Post' }}
          </v-card-title>
          <v-card-text>
            <v-text-field v-model="form.title" label="Title" />
            <v-textarea v-model="form.content" label="Content" rows="6" />
            <v-select
              v-model="form.status"
              :items="['draft', 'publish']"
              label="Status"
            />     
            <v-text-field
              v-model.number="form.priority"
              label="Priority"
              type="number"
            />
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="save">Save</v-btn>
            <v-btn text @click="closeDialog">Cancel</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </WordpressLayout>
</template>

<script setup>
import WordpressLayout from "@/Layouts/WordpressLayout.vue";
import { ref, onMounted } from "vue";
import axios from "axios";

const posts = ref([]);
const dialog = ref(false);
const editing = ref(null);
const form = ref({ title: "", content: "", status: "draft", priority: 0 });
const loading = ref(false);
const alertMessage = ref("");

const headers = [
  { text: "Title", value: "title" },
  { text: "Content", value: "content" },
  { text: "Status", value: "status" },
  { text: "Actions", value: "actions", sortable: false },
];

const fetch = async () => {
  const res = await axios.get("/back-office/api/posts");
  posts.value = res.data.data || res.data;
};

onMounted(fetch);

function openCreate() {
  editing.value = { isNew: true };
  form.value = { title: "", content: "", status: "draft", priority: 0 };
  dialog.value = true;
}

function edit(item) {
  editing.value = item;
  form.value = { ...item };
  dialog.value = true;
}

async function del(item) {
  if (!confirm("Delete post?")) return;
  await axios.delete(`/back-office/api/posts/${item.id}`);
  fetch();
}

async function save() {
  if (editing.value?.isNew) {
    await axios.post("/back-office/api/posts", form.value);
  } else {
    await axios.put(`/back-office/api/posts/${editing.value.id}`, form.value);
  }
  dialog.value = false;
  fetch();
}

function closeDialog() {
  dialog.value = false;
  editing.value = null;
}

async function sync() {
  loading.value = true;
  alertMessage.value = "Syncing posts from WordPress...";
  try {
    await axios.post("/back-office/api/sync");
    fetch();
  } catch (err) {
    alertMessage.value = "Failed to sync posts.";
  } finally {
    loading.value = false;
  }
}
</script>
