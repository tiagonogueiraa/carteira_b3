<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
} from '@/components/ui/card';

const form = useForm({
    ticker: '',
    type: '',
    quantity: '',
    price: '',
    purchased_at: '',
});

// descrição do autocomplete
// No jQuery, você teria uma variável solta (`let resultados = []`) e chamaria
// `.html()` manualmente pra atualizar a tela toda vez que ela mudasse.
// No Vue, `ref([])` cria uma variável "observada" (reativa) — quando ela muda,
// qualquer parte do <template> que a usa se atualiza sozinha, sem você
// precisar tocar no DOM manualmente.
const tickerSuggestions = ref([]);
const showSuggestions = ref(false);
let debounceTimer = null;
let ignoraProximaBusca = false;

// `watch` é o equivalente ao `.on('input', fn)` do jQuery: ele "escuta"
// quando `form.ticker` muda, e roda a função toda vez que isso acontece.
watch(() => form.ticker, (novoValor) => {

    // se ele clicar na lista, ignora 
    if (ignoraProximaBusca) {
        ignoraProximaBusca = false;
        return;
    }

    clearTimeout(debounceTimer);

    if (novoValor.length < 1) {
        tickerSuggestions.value = [];
        showSuggestions.value = false;
        return;
    }

    // Debounce: espera 300ms sem o usuário digitar mais nada antes de
    // buscar. Evita mandar uma requisição a cada tecla apertada.
    debounceTimer = setTimeout(async () => {
        const response = await axios.get('/tickers/search', {
            params: { ticker: novoValor },
        });

        tickerSuggestions.value = response.data;
        showSuggestions.value = true;
    }, 300);
});

// Quando clicar na lista setar o input
function selecionarTicker(ticker) {
    ignoraProximaBusca = true;
    form.ticker = ticker.symbol;
    showSuggestions.value = false;
    tickerSuggestions.value = [];
}

const submit = () => {
    form.post(route('stocks.store'));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Cadastrar ação" />

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Cadastrar ativo</CardTitle>
                        <CardDescription>
                            Registre uma compra de ação ou FII na sua carteira
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="ticker">Ticker</Label>
                                    <Input
                                        id="ticker"
                                        v-model="form.ticker"
                                        placeholder="Ex: PETR4"
                                        autocomplete="off"
                                        @focus="showSuggestions = tickerSuggestions.length > 0"
                                        required
                                    />
                                    <p v-if="form.errors.ticker" class="text-sm text-destructive">
                                        {{ form.errors.ticker }}
                                    </p>
                                    <ul
                                        v-if="showSuggestions && tickerSuggestions.length"
                                        class="absolute z-10 w-full text-popover-foreground border bg-popover rounded-md mt-1 shadow-lg max-h-60 overflow-auto"
                                    >
                                        <li
                                            v-for="ticker in tickerSuggestions"
                                            :key="ticker.symbol"
                                            @click="selecionarTicker(ticker)"
                                            class="px-3 py-2 hover:bg-muted cursor-pointer flex items-center gap-2"
                                        >
                                            <img
                                                v-if="ticker.logo_url"
                                                :src="ticker.logo_url"
                                                class="w-5 h-5"
                                            />
                                            <span class="font-medium">{{ ticker.symbol }}</span>
                                            <span class="text-sm text-muted-foreground">{{ ticker.name }}</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="space-y-2">
                                    <Label for="type">Tipo de ativo</Label>
                                    <Select v-model="form.type">
                                        <SelectTrigger id="type" class="w-full">
                                            <SelectValue placeholder="Selecione o tipo" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="acao">Ação</SelectItem>
                                            <SelectItem value="fii">FII</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.type" class="text-sm text-destructive">
                                        {{ form.errors.type }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="quantity">Quantidade</Label>
                                    <Input
                                        id="quantity"
                                        type="number"
                                        min="1"
                                        v-model="form.quantity"
                                        required
                                    />
                                    <p v-if="form.errors.quantity" class="text-sm text-destructive">
                                        {{ form.errors.quantity }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="price">Preço unitário</Label>
                                    <Input
                                        id="price"
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        v-model="form.price"
                                        required
                                    />
                                    <p v-if="form.errors.price" class="text-sm text-destructive">
                                        {{ form.errors.price }}
                                    </p>
                                </div>

                                <div class="space-y-2 sm:col-span-2">
                                    <Label for="purchased_at">Data da compra</Label>
                                    <Input
                                        id="purchased_at"
                                        type="date"
                                        v-model="form.purchased_at"
                                        required
                                    />
                                    <p v-if="form.errors.purchased_at" class="text-sm text-destructive">
                                        {{ form.errors.purchased_at }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <Button variant="outline" as-child>
                                    <Link :href="route('stocks.index')">Voltar</Link>
                                </Button>                            
                                <Button type="submit" :disabled="form.processing">
                                    Salvar ativo
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>