<script setup lang="ts">
import AddEmployeeDialog from '@/components/AddEmployeeDialog.vue';
import { Button } from '@/components/ui/button';
import UpdateEmployeeDialog from '@/components/UpdateEmployeeDialog.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

type Employee = {
    id: number;
    firstname: string;
    lastname: string;
    gender: 'male' | 'female' | 'non-binary' | 'prefer-not-to-say';
    birthday: string;
    monthly_salary: number;
    created_at: string;
    updated_at: string;
};

const employees = ref<Employee[]>();

const maleEmployeesCount = computed(() => {
    if (employees.value === undefined || !employees.value.length) return 0;
    return employees.value.filter((employee) => employee.gender === 'male').length;
});

const femaleEmployeesCount = computed(() => {
    if (employees.value === undefined || !employees.value.length) return 0;
    return employees.value.filter((employee) => employee.gender === 'female').length;
});

const averageEmployeesAge = computed(() => {
    if (employees.value === undefined || !employees.value.length) return 0;

    const today = new Date();
    const totalAge = employees.value.reduce((sum, employee) => {
        const birthDate = new Date(employee.birthday);
        const monthDiff = today.getMonth() - birthDate.getMonth();
        let age = today.getFullYear() - birthDate.getFullYear();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) age--;
        return sum + age;
    }, 0);

    return totalAge / employees.value.length;
});

const totalMonthlySalary = computed(() => {
    if (employees.value === undefined || !employees.value.length) return 0;

    return employees.value.reduce((total, employee) => {
        return total + employee.monthly_salary;
    }, 0);
});

async function getEmployees() {
    const response = await fetch('http://localhost:8000/api/employees');
    const data = await response.json();

    employees.value = data;
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <div
                    class="border-sidebar-border/70 dark:border-sidebar-border relative flex aspect-video flex-col overflow-hidden rounded-xl border p-4 transition-colors hover:bg-blue-100/40 dark:hover:bg-blue-900/40"
                >
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-mars-icon lucide-mars"
                        >
                            <path d="M16 3h5v5" />
                            <path d="m21 3-6.75 6.75" />
                            <circle cx="10" cy="14" r="6" />
                        </svg>

                        <p class="text-xs">Male Employees</p>
                    </div>

                    <div class="grid flex-1 place-items-center">
                        <p class="text-4xl font-bold text-blue-600">{{ maleEmployeesCount }}</p>
                    </div>
                </div>

                <div
                    class="border-sidebar-border/70 dark:border-sidebar-border relative flex aspect-video flex-col overflow-hidden rounded-xl border p-4 transition-colors hover:bg-red-100/40 dark:hover:bg-red-900/40"
                >
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-venus-icon lucide-venus"
                        >
                            <path d="M12 15v7" />
                            <path d="M9 19h6" />
                            <circle cx="12" cy="9" r="6" />
                        </svg>

                        <p class="text-xs">Female Employees</p>
                    </div>

                    <div class="grid flex-1 place-items-center">
                        <p class="text-4xl font-bold text-red-600 dark:text-red-400">{{ femaleEmployeesCount }}</p>
                    </div>
                </div>

                <div
                    class="border-sidebar-border/70 dark:border-sidebar-border relative flex aspect-video flex-col overflow-hidden rounded-xl border p-4 transition-colors hover:bg-green-100/40 dark:hover:bg-green-900/40"
                >
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-hash-icon lucide-hash"
                        >
                            <line x1="4" x2="20" y1="9" y2="9" />
                            <line x1="4" x2="20" y1="15" y2="15" />
                            <line x1="10" x2="8" y1="3" y2="21" />
                            <line x1="16" x2="14" y1="3" y2="21" />
                        </svg>

                        <p class="text-xs">Average Age of All Employees</p>
                    </div>

                    <div class="grid flex-1 place-items-center">
                        <p class="text-4xl font-bold text-green-600 dark:text-green-400">{{ averageEmployeesAge.toFixed(2) }}</p>
                    </div>
                </div>

                <div
                    class="border-sidebar-border/70 dark:border-sidebar-border relative flex aspect-video flex-col overflow-hidden rounded-xl border p-4 transition-colors hover:bg-orange-100/40 dark:hover:bg-orange-900/40"
                >
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-philippine-peso-icon lucide-philippine-peso"
                        >
                            <path d="M20 11H4" />
                            <path d="M20 7H4" />
                            <path d="M7 21V4a1 1 0 0 1 1-1h4a1 1 0 0 1 0 12H7" />
                        </svg>

                        <p class="text-xs">Total Monthly Salary of All Employees</p>
                    </div>

                    <div class="grid flex-1 place-items-center">
                        <p class="text-4xl font-bold text-orange-600 dark:text-orange-400">{{ totalMonthlySalary }}</p>
                    </div>
                </div>
            </div>

            <div class="relative flex min-h-[100vh] flex-1 flex-col gap-4 md:min-h-min">
                <div class="flex items-center justify-end gap-2">
                    <AddEmployeeDialog />
                    <Button @click="getEmployees">Refresh Employees Record</Button>
                </div>

                <div class="border-sidebar-border/70 dark:border-sidebar-border flex-1 rounded-xl border md:min-h-min">
                    <UpdateEmployeeDialog :employees />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
