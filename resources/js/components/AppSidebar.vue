<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { Globe, LayoutGrid, LogOut, Package, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard, home, logout } from '@/routes';
import { index as productsIndex } from '@/routes/products';
import { index as usersIndex } from '@/routes/admin/users';
import type { NavItem } from '@/types';

const page = usePage();

const handleLogout = () => { router.flushAll(); };

const canManageUsers = computed(
    () => page.props.currentTeam?.role === 'owner' || page.props.currentTeam?.role === 'admin'
);

const dashboardUrl = computed(() =>
    page.props.currentTeam ? dashboard(page.props.currentTeam.slug).url : '/',
);

const productsUrl = computed(() =>
    page.props.currentTeam ? productsIndex(page.props.currentTeam.slug).url : '/',
);

const usersUrl = computed(() =>
    page.props.currentTeam ? usersIndex(page.props.currentTeam.slug).url : '/',
);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboardUrl.value,
            icon: LayoutGrid,
        },
        {
            title: 'Produtos',
            href: productsUrl.value,
            icon: Package,
        },
    ];

    if (canManageUsers.value) {
        items.push({
            title: 'Usuários',
            href: usersUrl.value,
            icon: Users,
        });
    }

    return items;
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardUrl">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <SidebarMenu>
                <SidebarMenuItem>
                    <TeamSwitcher />
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child>
                        <a :href="home().url">
                            <Globe class="size-4" />
                            <span>Ver site</span>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child>
                        <Link :href="logout()" as="button" @click="handleLogout">
                            <LogOut class="size-4" />
                            <span>Sair</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <div class="border-t border-sidebar-border my-1" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
