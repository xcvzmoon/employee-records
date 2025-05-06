<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { NumberField, NumberFieldContent, NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput } from '@/components/ui/number-field';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { cn } from '@/lib/utils';
import { CalendarDate, DateFormatter, type DateValue, getLocalTimeZone } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

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

const genders = [
    {
        label: 'Male',
        value: 'male',
    },
    {
        label: 'Female',
        value: 'female',
    },
    {
        label: 'Non-Binary',
        value: 'non-binary',
    },
    {
        label: 'Prefer not to say',
        value: 'prefer-not-to-say',
    },
];

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

const { employees } = defineProps<{ employees?: Employee[] }>();

const id = ref<number>();
const firstname = ref<string>();
const lastname = ref<string>();
const gender = ref<string>();
const birthday = ref<DateValue>();
const monthlySalary = ref<number>();
const isOpen = ref(false);
const isValid = computed(() => firstname.value && lastname.value && gender.value && birthday.value && monthlySalary.value);

function resetModels() {
    id.value = undefined;
    firstname.value = undefined;
    lastname.value = undefined;
    gender.value = undefined;
    birthday.value = undefined;
    monthlySalary.value = undefined;
}

function closeDialog() {
    resetModels();
    isOpen.value = false;
}

function showDialog(employeeId: number) {
    if (employees === undefined || employees.length === 0) return;

    const employee = employees.find((employee) => employee.id === employeeId);

    if (employee === undefined) return;

    isOpen.value = true;

    id.value = employee.id;
    firstname.value = employee.firstname;
    lastname.value = employee.lastname;
    gender.value = employee.gender;
    monthlySalary.value = employee.monthly_salary;

    const _birthday = new Date(employee.birthday);

    birthday.value = new CalendarDate(_birthday.getFullYear(), _birthday.getMonth() + 1, _birthday.getDay() + 1);
}

async function updateEmployee() {
    if (!isValid.value) return;

    const response = await fetch(`http://localhost:8000/api/employees/${id.value}`, {
        method: 'PUT',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            firstname: firstname.value,
            lastname: lastname.value,
            gender: gender.value,
            birthday: new Date(birthday.value!.year, birthday.value!.month, birthday.value!.day),
            monthly_salary: monthlySalary.value,
        }),
    });

    if (response) closeDialog();
}

async function deleteEmployee() {
    if (!isValid.value) return;

    const response = await fetch(`http://localhost:8000/api/employees/${id.value}`, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
    });

    if (response.status === 204) closeDialog();
}
</script>

<template>
    <ul class="divide-y text-sm">
        <li v-for="employee in employees" :key="employee.id" class="group p-1" @click="showDialog(employee.id)">
            <div class="group-hover:bg-secondary grid grid-cols-12 rounded-md px-4 py-2">
                <p class="col-span-3">{{ employee.firstname }}</p>
                <p class="col-span-3">{{ employee.lastname }}</p>
                <p class="col-span-2 capitalize">{{ employee.gender.replaceAll('-', ' ') }}</p>
                <p class="col-span-2">{{ new Date(employee.birthday).toDateString() }}</p>
                <p class="col-span-2">{{ `PHP ${employee.monthly_salary}` }}</p>
            </div>
        </li>
    </ul>

    <Dialog v-bind:open="isOpen">
        <DialogContent disable-outside-pointer-events class="sm:max-w-[40rem]">
            <DialogHeader>
                <DialogTitle>Update Employee Record</DialogTitle>
                <DialogDescription>Answer all input fields. Click save when you're done.</DialogDescription>
            </DialogHeader>

            <div class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="firstname" class="text-right">First Name</Label>
                    <Input v-model="firstname" id="firstname" class="col-span-3" />
                </div>

                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="lastname" class="text-right">Last Name</Label>
                    <Input v-model="lastname" class="col-span-3" />
                </div>

                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="gender" class="text-right">Gender</Label>

                    <Select v-model="gender">
                        <SelectTrigger class="col-span-3 w-full">
                            <SelectValue />
                        </SelectTrigger>

                        <SelectContent>
                            <SelectGroup>
                                <SelectItem v-for="gender in genders" :key="gender.value" :value="gender.value">
                                    {{ gender.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>

                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="birthday" class="text-right">Birthday</Label>

                    <Popover>
                        <PopoverTrigger as-child>
                            <Button
                                id="birthday"
                                variant="outline"
                                :class="cn('col-span-3 justify-between text-left font-normal', !birthday && 'text-muted-foreground')"
                            >
                                <p>{{ birthday ? df.format(birthday.toDate(getLocalTimeZone())) : '' }}</p>
                                <CalendarIcon class="size-4" />
                            </Button>
                        </PopoverTrigger>

                        <PopoverContent class="w-auto p-0">
                            <Calendar v-model="birthday" initial-focus />
                        </PopoverContent>
                    </Popover>
                </div>

                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="monthly-salary" class="text-right">Monthly Salary</Label>

                    <NumberField
                        id="balance"
                        class="col-span-3"
                        :format-options="{
                            style: 'currency',
                            currency: 'PHP',
                            currencyDisplay: 'code',
                            currencySign: 'accounting',
                        }"
                        v-model="monthlySalary"
                    >
                        <NumberFieldContent>
                            <NumberFieldDecrement />
                            <NumberFieldInput />
                            <NumberFieldIncrement />
                        </NumberFieldContent>
                    </NumberField>
                </div>
            </div>

            <DialogFooter>
                <Button variant="outline" @click="closeDialog">Cancel</Button>
                <Button variant="destructive" @click="deleteEmployee">Delete</Button>
                <Button :disabled="!isValid" @click="updateEmployee">Update employee record</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
