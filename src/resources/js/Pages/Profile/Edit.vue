<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
} from '@/components/ui/card';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Profile
            </h2>
        </template>

        <div class="py-12">
            <Card>
                <CardContent>
                    <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                        <Card
                            class="p-4 shadow sm:rounded-lg sm:p-8"
                        >
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                                class="max-w-xl"
                            />
                        </Card>
        
                        <Card
                            class="p-4 shadow sm:rounded-lg sm:p-8"
                        >
                            <UpdatePasswordForm class="max-w-xl" />
                        </Card>
        
                        <Card
                            class="p-4 shadow sm:rounded-lg sm:p-8"
                        >
                            <DeleteUserForm class="max-w-xl" />
                        </Card>
                    </div>
                    
                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
