<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
} from '@/components/ui/card';
import {
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
    TableEmpty,
} from '@/components/ui/table';

defineProps({
    stocks: {
        type: Array,
        required: true,
    },
});

const destroy = (stock) => {
    if (confirm(`Remover ${stock.ticker} da carteira?`)) {
        router.delete(route('stocks.destroy', stock));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Ações" />

        <div class="py-12">
            <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-foreground">Minhas ações</h1>
                    <Button as-child>
                        <Link :href="route('stocks.create')">+ Nova ação</Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Carteira</CardTitle>
                        <CardDescription>Ações e FIIs cadastrados</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Ticker</TableHead>
                                    <TableHead>Tipo</TableHead>
                                    <TableHead class="text-right">Quantidade</TableHead>
                                    <TableHead class="text-right">Preço médio</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <template v-if="stocks.length > 0">
                                    <TableRow v-for="stock in stocks" :key="stock.id">
                                        <TableCell class="font-medium">
                                            <Link
                                                :href="route('stocks.show', stock)"
                                                class="hover:underline"
                                            >
                                                {{ stock.ticker }}
                                            </Link>
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant="secondary">
                                                {{ stock.type === 'acao' ? 'Ação' : 'FII' }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            {{ stock.quantity }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            R$ {{ Number(stock.average_price).toFixed(2) }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button variant="ghost" size="sm" as-child>
                                                    <Link :href="route('stocks.edit', stock)">
                                                        Editar
                                                    </Link>
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="destroy(stock)"
                                                >
                                                    Remover
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </template>
                                <TableEmpty v-else :colspan="5">
                                    Nenhuma ação cadastrada ainda.
                                </TableEmpty>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
