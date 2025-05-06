<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { NumberField, NumberFieldContent, NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput } from '@/components/ui/number-field';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { cn } from '@/lib/utils';
import { DateFormatter, type DateValue, getLocalTimeZone } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

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

const firstname = ref<string>();
const lastname = ref<string>();
const gender = ref<string>();
const birthday = ref<DateValue>();
const monthlySalary = ref<number>();
const isOpen = ref(false);
const isValid = computed(() => firstname.value && lastname.value && gender.value && birthday.value && monthlySalary.value);

function resetModels() {
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

async function createEmployee() {
    if (!isValid.value) return;

    const response = await fetch('http://localhost:8000/api/employees', {
        method: 'POST',
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
</script>

<template>
    <Dialog v-bind:open="isOpen">
        <DialogTrigger as-child>
            <Button @click="isOpen = true">Add new employee record</Button>
        </DialogTrigger>

        <DialogContent disable-outside-pointer-events class="sm:max-w-[40rem]">
            <DialogHeader>
                <DialogTitle>New Employee Record</DialogTitle>
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
                <Button :disabled="!isValid" @click="createEmployee">Save employee record</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
