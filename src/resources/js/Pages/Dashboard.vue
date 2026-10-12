<script setup>
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import VueApexCharts from 'vue3-apexcharts';
import { useDarkMode } from '@/Composables/Usedarkmode';
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

import { Wallet, LineChart, TrendingUp, TrendingDown, HandCoins } from '@lucide/vue';

import ptBr from 'apexcharts/dist/locales/pt-br.json';

import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';

const currencyFormatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
});
const currencyFormatterCompact = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
    maximumFractionDigits: 0,
});

const props = defineProps({
    stocks: {
        type: Array,
        required: true,
    },
    netWorthHistory: {
        type: Array,
        required: true,
    },
    stocksHistory: {
        type: Object,
        required: true,
    },
    dailyHistory: {
        type: Array,
        required: true,
    },
    periodoSelecionado: {
        type: String,
        default: '7d',
    },
});

console.log('props.stocksHistory', props.stocksHistory);
const { isDark } = useDarkMode();

// Mesmos valores do :root/.dark do app.css. O ApexCharts desenha em <canvas>/SVG
// próprio, então não enxerga var(--chart-1) direto — por isso os valores ficam
// duplicados aqui, escolhidos com base no isDark em vez de lidos via CSS.
const chartColor = computed(() => (isDark.value ? 'hsl(220 70% 50%)' : 'hsl(12 76% 61%)'));
const mutedColor = computed(() => (isDark.value ? 'hsl(0 0% 63.9%)' : 'hsl(0 0% 45.1%)'));
const borderColor = computed(() => (isDark.value ? 'hsl(0 0% 14.9%)' : 'hsl(0 0% 89.8%)'));

const chartSeriesDiario = computed(() => [
    { name: 'Valor de mercado', data: props.dailyHistory.map(d => d.mercado) },
    { name: 'Valor com dividendos', data: props.dailyHistory.map(d => d.comDividendos) },
]);

const tipoSelecionado = ref('todas');

const chartOptions = computed(() => ({
    
    chart: {
        toolbar: { show: false },
        background: 'transparent',
        fontFamily: 'inherit',
        locales: [ptBr],
        defaultLocale: 'pt-br',
    },
    colors: [
        chartColor.value,                          // Valor de mercado
        isDark.value ? 'hsl(142 71% 45%)' : 'hsl(142 76% 36%)', // Valor com dividendos (verde)
    ],
    stroke: {
        curve: 'smooth',
        width: 2,
    },
    markers: {
        size: 4,
        // colors: [chartColor.value],
    },
    dataLabels: { enabled: false },
    legend: { show: true },
    grid: {
        borderColor: borderColor.value,
        strokeDashArray: 4,
    },
    xaxis: {
        categories: props.dailyHistory.map((d) => d.date),
        tickAmount: 10, // evita 365 rótulos amontoados quando o período for "1y"
        labels: { style: { colors: mutedColor.value } },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: {
            style: { colors: mutedColor.value },
            formatter: (value) => currencyFormatterCompact.format(Number(value)),
        },
    },
    tooltip: {
        theme: isDark.value ? 'dark' : 'light',
        y: { formatter: (value) => currencyFormatter.format(Number(value)) },
    },
}));

const chartSeriesAcoesGroup = computed(() => {
    // mapa ticker -> tipo, pra saber de qual grupo cada série é
    const tipoPorTicker = {};
    stocksComTotais.value.forEach((s) => {
        tipoPorTicker[s.ticker] = s.type;
    });

    // soma, dia a dia, todas as séries do mesmo tipo
    const grupos = {};
    props.stocksHistory.series.forEach((serie) => {
        const tipo = tipoPorTicker[serie.name];
        if (!tipo) return;

        if (!grupos[tipo]) {
            grupos[tipo] = serie.data.map(() => 0);
        }

        grupos[tipo] = grupos[tipo].map((soma, i) => soma + serie.data[i]);
    });

    const labelPorTipo = { acao: 'Ações', fii: 'FIIs' };
    const linhas = Object.entries(grupos).map(([tipo, data]) => ({
        name: labelPorTipo[tipo] ?? tipo,
        data,
    }));

    if (tipoSelecionado.value === 'todas') {
        return linhas;
    }

    return linhas.filter((linha) => linha.name === labelPorTipo[tipoSelecionado.value]);
});

const chartSeriesAcoesDetalhado = computed(() => {
    if (tipoSelecionado.value === 'todas') {
        return props.stocksHistory.series;
    }

    const tickersDoTipo = stocksComTotais.value
        .filter((s) => s.type === tipoSelecionado.value)
        .map((s) => s.ticker);

    return props.stocksHistory.series.filter((s) => tickersDoTipo.includes(s.name));
});


const periodoLabel = computed(() => {
    const labels = { '1d': '1D', '7d': '7D', '30d': '30D', '1y': '1A' };
    return labels[props.periodoSelecionado] ?? props.periodoSelecionado;
});

const chartAcoesGroup = computed(() => ({
    chart: {
        toolbar: { show: false },
        background: 'transparent',
        fontFamily: 'inherit',
        locales: [ptBr],
        defaultLocale: 'pt-br',
    },
    stroke: {
        curve: 'smooth',
        width: 2,
    },
    markers: {
        size: 4,
    },
    dataLabels: { enabled: false },
    legend: {
        show: true,
        labels: { colors: mutedColor.value },
    },
    grid: {
        borderColor: borderColor.value,
        strokeDashArray: 4,
    },
    xaxis: {
        categories: props.stocksHistory.categories,
        tickAmount: 10, // evita 365 rótulos amontoados quando o período for "1y"
        labels: { style: { colors: mutedColor.value } },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: {
            style: { colors: mutedColor.value },
            formatter: (value) => currencyFormatterCompact.format(Number(value)),
        },
    },
    tooltip: {
        theme: isDark.value ? 'dark' : 'light',
        y: { formatter: (value) => currencyFormatter.format(Number(value)) },
    },
}));


const totalInvestido = computed(() =>
    props.stocks.reduce(
        (sum, stock) => sum + stock.quantity * Number(stock.average_price),
        0
    )
);

const totalMercado = computed(() =>
    props.stocks.reduce((sum, stock) => {
        const precoAtual = stock.market?.regular_market_price ?? stock.average_price;
        return sum + stock.quantity * Number(precoAtual) ;
    }, 0)
);



// TOTAL DIVIDENDOS
const totalDividendos = computed(() =>
props.stocks.reduce((sum, stock) => sum + Number(stock.total_dividends), 0)
);

// SOMA TOTAIS TABELAS
const stocksComTotais = computed(() =>
    props.stocks.map((stock) => {
        const investido = stock.quantity * Number(stock.average_price);
        const precoAtual = stock.market?.regular_market_price ?? stock.average_price;
        const mercado = stock.quantity * Number(precoAtual);
        const percentual = investido === 0 ? 0 : ((mercado - investido) / investido) * 100;
        const dividendos = stock.total_dividends;
        
        return { ...stock, investido, mercado, percentual, dividendos };
    })
);

// GRÁFICO ALOCAÇÃO POR ATIVO
// : percorre stocksComTotais e soma o mercado de cada ação dentro do grupo do seu type — resultado: 
// { acao: 15000, fii: 8000 }, depois convertido num array (alocacaoPorTipo) que guarda tipo
//  + rótulo amigável + total, útil tanto pro labels do gráfico quanto pra identificar o grupo no clique.
//  Monta dinamicamente (se você só tiver FIIs, por exemplo, não aparece uma fatia vazia de "Ações").
const alocacaoPorTipo = computed(() => {
    const grupos = {};
    stocksComTotais.value.forEach((s) => {
        grupos[s.type] = (grupos[s.type] ?? 0) + s.mercado;
    });

    const labelPorTipo = { acao: 'Ações', fii: 'FIIs' };

    return Object.entries(grupos).map(([tipo, total]) => ({
        tipo,
        label: labelPorTipo[tipo] ?? tipo,
        total,
    }));
});

const chartSeriesAlocacao = computed(() => alocacaoPorTipo.value.map((g) => g.total));

const chartOptionsAlocacao = computed(() => ({
    chart: {
        type: 'donut',
        background: 'transparent',
        fontFamily: 'inherit',
        events: {
            dataPointSelection: (event, chartContext, config) => {
                const grupo = alocacaoPorTipo.value[config.dataPointIndex];
                tipoSelecionado.value = tipoSelecionado.value === grupo.tipo ? 'todas' : grupo.tipo;
            },
        },
    },
    labels: alocacaoPorTipo.value.map((g) => g.label),
    legend: { position: 'bottom', labels: { colors: mutedColor.value } },
    dataLabels: { formatter: (val) => `${val.toFixed(1)}%` },
    tooltip: {
        theme: isDark.value ? 'dark' : 'light',
        y: { formatter: (value) => currencyFormatter.format(Number(value)) },
    },
}));

// total com dividendos
const totalComDividendos = computed(() => totalMercado.value + totalDividendos.value);

const percentualGanho = computed(() => {
    if (totalInvestido.value === 0) return 0;
    return ((totalComDividendos.value - totalInvestido.value) / totalInvestido.value) * 100;
});
console.log('total mercado  ', totalMercado.value);
console.log('total dividendos', totalDividendos.value);
function mudarPeriodo(novoPeriodo) {
    router.get(route('dashboard'), { periodo: novoPeriodo }, { preserveState: true, preserveScroll: true, replace: true });
}

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Minhas ações
            </h2>
        </template>
        
        <div class="py-12">
            <div class="p-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Tabs :model-value="periodoSelecionado" @update:model-value="mudarPeriodo" class="mb-4">
                    <TabsList>
                        <TabsTrigger value="1d">1 dia</TabsTrigger>
                        <TabsTrigger value="7d">7 dias</TabsTrigger>
                        <TabsTrigger value="30d">30 dias</TabsTrigger>
                        <TabsTrigger value="1y">1 ano</TabsTrigger>
                    </TabsList>
                </Tabs>
                <div class="mx-auto max-w-7xl grid gap-4 sm:grid-cols-5">
                   <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardDescription>Total investido</CardDescription>
                            <Wallet class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <CardTitle>{{ currencyFormatter.format(totalInvestido) }}</CardTitle>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardDescription>Valor de mercado</CardDescription>
                            <LineChart class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <CardTitle>{{ currencyFormatter.format(totalMercado) }}</CardTitle>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardDescription>Valor com dividendos</CardDescription>
                            <HandCoins class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <CardTitle>{{ currencyFormatter.format(totalComDividendos) }}</CardTitle>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardDescription>Rentabilidade</CardDescription>
                            <TrendingUp class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <CardTitle :class="percentualGanho >= 0 ? 'text-green-600' : 'text-red-600'">
                                {{ percentualGanho >= 0 ? '+' : '' }}{{ percentualGanho.toFixed(2) }}%
                            </CardTitle>             
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardDescription>Total em dividendos</CardDescription>
                            <TrendingDown class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <CardTitle>
                                {{ currencyFormatter.format(totalDividendos) }}
                            </CardTitle>
                        </CardContent>                        
                    </Card>
                </div>
            </div>
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Patrimônio investido</CardTitle>
                        <CardDescription>
                            Capital acumulado nos últimos 6 meses (não inclui
                            valorização de mercado ainda)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <VueApexCharts
                            type="line"
                            height="280"
                            :options="chartOptions"
                            :series="chartSeriesDiario"
                        />
                    </CardContent>
                </Card>
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Patrimônio investido AÇÕES</CardTitle>
                            <CardDescription>
                                {{ tipoSelecionado === 'todas' ? 'Ações vs FIIs' : (tipoSelecionado === 'acao' ? 'Ações' : 'FIIs') }} — valor de mercado ({{ periodoLabel }})
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <VueApexCharts type="line" height="280" :options="chartAcoesGroup" :series="chartSeriesAcoesGroup" />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Alocação por tipo</CardTitle>
                            <CardDescription>Clique numa fatia pra filtrar o gráfico ao lado</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <VueApexCharts type="donut" height="280" :options="chartOptionsAlocacao" :series="chartSeriesAlocacao" />
                        </CardContent>
                    </Card>
                </div>


                <Card>
                    <CardHeader>
                        <CardTitle>Patrimônio investido AÇÔES</CardTitle>
                        <CardDescription>
                            Capital acumulado nos últimos 6 meses (não inclui
                            valorização de mercado ainda)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <VueApexCharts
                            type="line"
                            height="280"
                            :options="chartAcoesGroup"
                            :series="chartSeriesAcoesDetalhado"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Minhas ações</CardTitle>
                        <CardDescription>Resumo da carteira</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Ticker</TableHead>
                                    <TableHead>Tipo</TableHead>
                                    <TableHead class="text-right">Quantidade</TableHead>
                                    <TableHead class="text-right">Preço médio</TableHead>
                                    <TableHead class="text-right">Preço de mercado</TableHead>
                                    <TableHead class="text-right">Total investido</TableHead>
                                    <TableHead class="text-right">Total mercado</TableHead>
                                    <TableHead class="text-right">Rentabilidade</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <template v-if="stocksComTotais.length > 0">
                                    <TableRow v-for="stock in stocksComTotais" :key="stock.id">
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
                                            {{ currencyFormatter.format(Number(stock.average_price)) }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            {{ stock.market?.regular_market_price
                                                ? currencyFormatter.format(Number(stock.market.regular_market_price))
                                                : '—' }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            {{ currencyFormatter.format(stock.investido) }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            {{ currencyFormatter.format(stock.mercado) }}
                                        </TableCell>
                                        <TableCell
                                            class="text-right"
                                            :class="stock.percentual >= 0 ? 'text-green-600' : 'text-red-600'"
                                        >
                                            {{ stock.percentual >= 0 ? '+' : '' }}{{ stock.percentual.toFixed(2) }}%
                                        </TableCell>

                                    </TableRow>
                                </template>
                                <TableEmpty v-else :colspan="8">
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
