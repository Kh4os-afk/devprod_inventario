<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="shortcut icon" href="{{ asset('imagens/logo_preto.png') }}" type="image/x-icon">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet"/>
    <title>{{ config('app.name') }}</title>
    @vite('resources/css/app.css')
    @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>
    <flux:brand href="#" logo="{{ asset('imagens/logo_preto.png') }}" name="{{ config('app.name') }}" class="px-2 dark:hidden"/>
    <flux:brand href="#" logo="{{ asset('imagens/logo_branco.png') }}" name="{{ config('app.name') }}" class="px-2 hidden dark:flex"/>
    <flux:input as="button" variant="filled" placeholder="Search..." icon="magnifying-glass"/>
    <flux:navlist variant="outline">
        <flux:navlist.item icon="home" href="/">Dashboard</flux:navlist.item>
        <flux:navlist.item icon="inbox" href="/inventario">Inventarios</flux:navlist.item>
        {{--<flux:navlist.group expandable heading="Favorites" class="hidden lg:grid">
            <flux:navlist.item href="#">Marketing site</flux:navlist.item>
            <flux:navlist.item href="#">Android app</flux:navlist.item>
            <flux:navlist.item href="#">Brand guidelines</flux:navlist.item>
        </flux:navlist.group>--}}
    </flux:navlist>
    <flux:spacer/>
    <flux:navlist variant="outline">
        <flux:navlist.item icon="cog-6-tooth" href="#">Settings</flux:navlist.item>
        <flux:navlist.item icon="information-circle" href="#">Help</flux:navlist.item>
    </flux:navlist>
    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <flux:profile avatar="https://fluxui.dev/img/demo/user.png" name="Olivia Martin"/>
        <flux:menu>
            <flux:menu.radio.group>
                <flux:menu.radio checked>Olivia Martin</flux:menu.radio>
                <flux:menu.radio>Truly Delta</flux:menu.radio>
            </flux:menu.radio.group>
            <flux:menu.separator/>
            <flux:menu.item icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>
{{--<flux:header class="block! bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <flux:navbar class="lg:hidden w-full">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>
        <flux:spacer/>
        <flux:dropdown position="top" align="start">
            <flux:profile avatar="https://fluxui.dev/img/demo/user.png"/>
            <flux:menu>
                <flux:menu.radio.group>
                    <flux:menu.radio checked>Olivia Martin</flux:menu.radio>
                    <flux:menu.radio>Truly Delta</flux:menu.radio>
                </flux:menu.radio.group>
                <flux:menu.separator/>
                <flux:menu.item icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:navbar>
    <flux:navbar scrollable>
        <flux:navbar.item href="#" current>Dashboard</flux:navbar.item>
    </flux:navbar>
</flux:header>--}}
<flux:main>
    {{ $slot }}
</flux:main>
@fluxScripts
</body>
</html>
