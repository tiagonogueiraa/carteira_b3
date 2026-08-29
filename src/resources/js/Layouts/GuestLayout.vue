<script setup>
import { Link } from '@inertiajs/vue3';

import { useDarkMode } from '@/Composables/Usedarkmode';
import { Button } from '@/components/ui/button';
import { Sun, Moon } from '@lucide/vue';

const { isDark, toggleDark } = useDarkMode();
</script>

<template>
    <!--
        Mesmo fundo escuro + acento verde da Home (Welcome.vue), pra Login e
        Registro parecerem parte do mesmo produto, não uma tela solta do
        Breeze. A classe "dark" na raiz ativa as variantes dark: dos
        componentes shadcn-vue (Input, Button, Card) usados dentro do slot —
        sem ela, esses componentes renderizam com o estilo de tema claro.
    -->
   <div class="relative flex min-h-screen flex-col items-center justify-center gap-6 bg-background px-6 py-12 text-foreground">
        
        <Button variant="ghost" size="icon" class="absolute right-4 top-4" @click="toggleDark">
            <Sun v-if="isDark" class="h-5 w-5" />
            <Moon v-else class="h-5 w-5" />
        </Button>

        <Link href="/" class="text-lg font-semibold tracking-tight">
            Minha Carteira
        </Link>

        <div class="w-full sm:max-w-md">
            <slot />
        </div>
    </div>
</template>
