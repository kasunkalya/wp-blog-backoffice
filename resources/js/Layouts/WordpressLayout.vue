<template>
  <v-app>
    <!-- Top Bar -->
    <v-app-bar app color="white" elevation="1">
      <v-toolbar-title class="font-weight-bold text-primary">
        WP Back Office
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon>
        <v-icon>mdi-bell</v-icon>
      </v-btn>
      <v-menu>
        <template #activator="{ props }">
          <v-btn icon v-bind="props">
            <v-avatar size="32">
              <v-icon>mdi-account</v-icon>
            </v-avatar>
          </v-btn>
        </template>
        <v-list>
          <v-list-item>
            <v-list-item-title>Profile</v-list-item-title>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>Logout</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <!-- Sidebar -->
    <v-navigation-drawer app permanent color="#23282d" dark>
      <v-list dense>
        <v-list-item>
          <v-list-item-title class="font-weight-bold">Dashboard</v-list-item-title>
        </v-list-item>
        <v-divider></v-divider>

        <v-list-item prepend-icon="mdi-post-outline" link>
          <v-list-item-title>Posts</v-list-item-title>
        </v-list-item>        
        <v-list-item prepend-icon="mdi-account-group" link>
          <v-list-item-title>Users</v-list-item-title>
        </v-list-item>
        <v-list-item prepend-icon="mdi-cog" link>
          <v-list-item-title>Settings</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- Main Content -->
    <v-main>
      <v-container fluid class="pa-6">
        <slot />
      </v-container>
    </v-main>
  </v-app>
</template>
