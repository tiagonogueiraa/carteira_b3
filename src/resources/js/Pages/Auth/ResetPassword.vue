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

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Redefinir senha" />

        <Card class="border-zinc-800 bg-zinc-900">
            <CardHeader>
                <CardTitle class="text-zinc-50">Redefinir senha</CardTitle>
                <CardDescription>Escolha uma nova senha</CardDescription>
            </CardHeader>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <p
                            v-if="form.errors.email"
                            class="text-sm text-red-400"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="password">Nova senha</Label>
                        <Input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-sm text-red-400"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="password_confirmation">
                            Confirmar nova senha
                        </Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <p
                            v-if="form.errors.password_confirmation"
                            class="text-sm text-red-400"
                        >
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <Button
                        type="submit"
                        class="w-full bg-emerald-500 text-zinc-950 hover:bg-emerald-400"
                        :disabled="form.processing"
                    >
                        Redefinir senha
                    </Button>
                </form>
            </CardContent>
        </Card>
    </GuestLayout>
</template>
