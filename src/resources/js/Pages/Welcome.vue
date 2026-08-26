<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
    CardFooter,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    Landmark,
    LineChart,
    History,
    PieChart,
    ShieldCheck,
    Lock,
    Check,
} from '@lucide/vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

// Conteúdo da seção "Recursos" centralizado num array — pra adicionar/remover
// um card de feature, só mexe aqui, não precisa tocar no template.
const features = [
    {
        icon: Landmark,
        title: 'Cadastro manual de ativos',
        description:
            'Registre ações e FIIs sem conectar sua conta bancária ou corretora.',
    },
    {
        icon: LineChart,
        title: 'Cotação atualizada',
        description: 'Preços buscados automaticamente na brapi.dev.',
    },
    {
        icon: History,
        title: 'Histórico de compras',
        description:
            'Cada lote registrado, com seu preço médio calculado de verdade.',
    },
    {
        icon: PieChart,
        title: 'Ganhos e perdas',
        description:
            'Veja o desempenho de cada ativo e da carteira como um todo.',
    },
    {
        icon: ShieldCheck,
        title: 'Ações e FIIs juntos',
        description: 'Uma visão única para os dois tipos de investimento.',
    },
    {
        icon: Lock,
        title: 'Seus dados, sua privacidade',
        description:
            'Sem acesso a conta bancária ou corretora — só o que você cadastra.',
    },
];

// Os dois planos exibidos na seção "Planos". "highlight" só controla o
// destaque visual do card (borda colorida), não muda a lógica de negócio.
const plans = [
    {
        name: 'Free',
        price: 'R$ 0',
        period: 'para sempre',
        highlight: false,
        items: [
            'Até 10 ativos cadastrados',
            'Cotação atualizada',
            'Histórico de lotes de compra',
        ],
        cta: 'Começar grátis',
        ctaDisabled: false,
    },
    {
        name: 'Pago',
        price: 'em breve',
        period: 'preço a definir',
        highlight: true,
        items: [
            'Ativos ilimitados',
            'Alertas de preço',
            'Exportação de relatório',
            'Histórico ilimitado de lotes',
        ],
        cta: 'Em breve',
        ctaDisabled: true,
    },
];
</script>

<template>
    <Head title="Minha Carteira" />

    <!--
        Fundo escuro fixo (não depende do toggle claro/escuro do resto do
        app, que ainda nem existe) pra reproduzir a estética do design de
        referência: fundo quase preto + acento verde nos destaques.
    -->
    <div class="min-h-screen bg-zinc-950 text-zinc-50">
        <div class="mx-auto max-w-6xl px-6">
            <!-- ===================== HEADER ===================== -->
            <!-- Logo + navegação de autenticação (Entrar/Registrar), igual
                 ao Welcome.vue original, só que restyled pro tema escuro. -->
            <header class="flex items-center justify-between py-8">
                <span class="text-lg font-semibold tracking-tight">
                    Minha Carteira
                </span>

                <nav v-if="canLogin" class="flex items-center gap-3">
                    <Button v-if="$page.props.auth.user" as-child>
                        <Link :href="route('dashboard')">Dashboard</Link>
                    </Button>

                    <template v-else>
                        <Button variant="ghost" as-child>
                            <Link :href="route('login')">Entrar</Link>
                        </Button>

                        <Button
                            v-if="canRegister"
                            class="bg-emerald-500 text-zinc-950 hover:bg-emerald-400"
                            as-child
                        >
                            <Link :href="route('register')">
                                Criar conta
                            </Link>
                        </Button>
                    </template>
                </nav>
            </header>

            <!-- ===================== HERO ===================== -->
            <!-- Chamada principal: o que é o produto e o CTA de cadastro.
                 "Criar conta grátis" repete o CTA do header pra quem já
                 rolou a página e perdeu o botão do topo de vista. -->
            <section class="py-20 text-center">
                <h1
                    class="mx-auto max-w-3xl text-4xl font-bold tracking-tight sm:text-5xl"
                >
                    Sua carteira de ações e FIIs,
                    <span class="text-emerald-400">sob seu controle</span>
                </h1>

                <p class="mx-auto mt-6 max-w-xl text-zinc-400">
                    Cadastre seus ativos manualmente, acompanhe cotações
                    atualizadas da B3 e veja sua rentabilidade em um só
                    lugar — sem conectar sua conta bancária.
                </p>

                <div class="mt-8 flex justify-center gap-4">
                    <Button
                        v-if="canRegister"
                        size="lg"
                        class="bg-emerald-500 text-zinc-950 hover:bg-emerald-400"
                        as-child
                    >
                        <Link :href="route('register')">
                            Criar conta grátis
                        </Link>
                    </Button>

                    <Button v-if="canLogin" size="lg" variant="outline" as-child>
                        <Link :href="route('login')">Já tenho conta</Link>
                    </Button>
                </div>
            </section>

            <!-- ============== FAIXA DE CONFIANÇA ============== -->
            <!-- Substitui os "logos de clientes" comuns em landing pages
                 (aqui não faria sentido, é um projeto pessoal) por
                 afirmações honestas sobre como o produto funciona. -->
            <section
                class="grid gap-4 border-y border-zinc-800 py-8 text-sm text-zinc-400 sm:grid-cols-3"
            >
                <div class="flex items-center justify-center gap-2">
                    <Lock class="size-4 text-emerald-400" />
                    Sem integração bancária
                </div>
                <div class="flex items-center justify-center gap-2">
                    <LineChart class="size-4 text-emerald-400" />
                    Cotações públicas via brapi.dev
                </div>
                <div class="flex items-center justify-center gap-2">
                    <Landmark class="size-4 text-emerald-400" />
                    Feito para ações e FIIs da B3
                </div>
            </section>

            <!-- ===================== RECURSOS ===================== -->
            <!-- Grid gerado a partir do array `features` lá em cima do
                 <script>. Cada item vira um Card do shadcn-vue. -->
            <section class="py-20">
                <h2 class="text-center text-3xl font-bold tracking-tight">
                    Tudo que você precisa pra acompanhar sua carteira
                </h2>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="feature in features"
                        :key="feature.title"
                        class="border-zinc-800 bg-zinc-900"
                    >
                        <CardHeader>
                            <div
                                class="mb-2 flex size-10 items-center justify-center rounded-full bg-emerald-500/10"
                            >
                                <component
                                    :is="feature.icon"
                                    class="size-5 text-emerald-400"
                                />
                            </div>
                            <CardTitle class="text-zinc-50">
                                {{ feature.title }}
                            </CardTitle>
                            <CardDescription>
                                {{ feature.description }}
                            </CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </section>

            <!-- ===================== PLANOS ===================== -->
            <!-- Gerado a partir do array `plans`. O plano pago ainda não
                 tem cobrança real implementada — o botão fica desabilitado
                 ("Em breve") até essa feature existir de verdade. -->
            <section class="py-20">
                <h2 class="text-center text-3xl font-bold tracking-tight">
                    Um plano para cada tamanho de carteira
                </h2>

                <div
                    class="mx-auto mt-12 grid max-w-3xl gap-6 sm:grid-cols-2"
                >
                    <Card
                        v-for="plan in plans"
                        :key="plan.name"
                        class="bg-zinc-900"
                        :class="
                            plan.highlight
                                ? 'border-emerald-500/50'
                                : 'border-zinc-800'
                        "
                    >
                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <CardTitle class="text-zinc-50">
                                    {{ plan.name }}
                                </CardTitle>
                                <Badge
                                    v-if="plan.highlight"
                                    class="bg-emerald-500 text-zinc-950"
                                >
                                    Mais recursos
                                </Badge>
                            </div>
                            <CardDescription>
                                <span class="text-2xl font-bold text-zinc-50">
                                    {{ plan.price }}
                                </span>
                                <span class="ml-1">{{ plan.period }}</span>
                            </CardDescription>
                        </CardHeader>

                        <CardContent>
                            <ul class="space-y-2 text-sm text-zinc-300">
                                <li
                                    v-for="item in plan.items"
                                    :key="item"
                                    class="flex items-center gap-2"
                                >
                                    <Check class="size-4 text-emerald-400" />
                                    {{ item }}
                                </li>
                            </ul>
                        </CardContent>

                        <CardFooter>
                            <Button
                                class="w-full"
                                :disabled="plan.ctaDisabled"
                                :class="
                                    !plan.ctaDisabled &&
                                    'bg-emerald-500 text-zinc-950 hover:bg-emerald-400'
                                "
                                :as-child="!plan.ctaDisabled && canRegister"
                            >
                                <Link
                                    v-if="!plan.ctaDisabled && canRegister"
                                    :href="route('register')"
                                >
                                    {{ plan.cta }}
                                </Link>
                                <template v-else>{{ plan.cta }}</template>
                            </Button>
                        </CardFooter>
                    </Card>
                </div>
            </section>

            <!-- ===================== FOOTER ===================== -->
            <footer class="border-t border-zinc-800 py-10 text-center text-sm text-zinc-500">
                Minha Carteira · construído com Laravel v{{ laravelVersion }}
                (PHP v{{ phpVersion }})
            </footer>
        </div>
    </div>
</template>
