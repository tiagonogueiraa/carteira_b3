<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
} from '@/components/ui/card';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirmar senha" />

        <Card class="border-zinc-800 bg-zinc-900">
            <CardHeader>
                <CardTitle class="text-zinc-50">Confirmar senha</CardTitle>
                <CardDescription>
                    Esta é uma área segura da aplicação. Confirme sua senha
                    antes de continuar.
                </CardDescription>
            </CardHeader>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="password">Senha</Label>
                        <Input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            autofocus
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-sm text-red-400"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <Button
                        type="submit"
                        class="w-full bg-emerald-500 text-zinc-950 hover:bg-emerald-400"
                        :disabled="form.processing"
                    >
                        Confirmar
                    </Button>
                </form>
            </CardContent>
        </Card>
    </GuestLayout>
</template>
