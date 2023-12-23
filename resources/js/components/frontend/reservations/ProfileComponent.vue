<template>
    <!-- <section class="col-12"> -->
        <!-- <BreadcrumbComponent />
    </div> -->

    <LoadingComponent :props="loading" />
    <section class="pt-7 pb-16">
        <div class="container max-w-[550px]">
            <router-link :to="{ name: 'frontend.home' }" class="mb-3 inline-flex items-center gap-2 text-primary">
                <i class="lab lab-undo lab-font-size-16"></i>
                <span class="text-xs font-medium leading-6">{{ $t('label.back_to_home') }}</span>
            </router-link>
            <div class="col-12">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t("button.add_reservation") }}</h3>
                    </div>

                    <div class="db-card-body">
                        <form @submit.prevent="save">
                            <div class="row">
                                <div class="form-col-12 sm:form-col-6">
                                    <label for="name" class="db-field-title required">{{ $t('label.name') }}</label>
                                    <input type="text" id="name" class="db-field-control" v-model="form.name"
                                        :class="errors.name ? 'invalid' : ''">
                                    <small class="db-field-alert" v-if="errors.name">
                                        {{ errors.name[0] }}
                                    </small>
                                </div>

                                <div class="form-col-12 sm:form-col-6">
                                    <label for="name" class="db-field-title required">{{ $t('label.phone') }}</label>
                                    <div class="w-full h-10 rounded-lg border px-4 flex items-center border-[#D9DBE9]" :class="errors.phone ? 'invalid' : ''">
                                        <div class="w-fit flex-shrink-0 dropdown-group">
                                            <button type="button" class="flex items-center gap-1 dropdown-btn">
                                                {{ flag }}
                                                <span class="whitespace-nowrap flex-shrink-0 text-xs">
                                                    {{ form.country_code }}
                                                </span>
                                                <input type="hidden" v-model="form.country_code">
                                            </button>
                                        </div>
                                        <input id="phone" type="text" v-on:keypress="phoneNumber($event)" v-model="form.phone"
                                            class="pl-4 text-sm w-full h-full text-heading">
                                    </div>
                                    <small class="db-field-alert" v-if="errors.phone">
                                        {{ errors.phone[0] }}
                                    </small>
                                </div>

                                <div class="form-col-12 sm:form-col-6">
                                    <label for="email" class="db-field-title required">{{ $t('label.email') }}</label>
                                    <input type="text" id="email" class="db-field-control" v-model="form.email"
                                        :class="errors.email ? 'invalid' : ''">
                                    <small class="db-field-alert" v-if="errors.email">
                                        {{ errors.email[0] }}
                                    </small>
                                </div>

                                <div class="form-col-12 sm:form-col-6">
                                    <label for="no_of_guests" class="db-field-title required">{{ $t('label.no_of_guests') }}</label>
                                    <input type="number" id="no_of_guests" class="db-field-control" v-model="form.no_of_guests"
                                        :class="errors.no_of_guests ? 'invalid' : ''">
                                    <small class="db-field-alert" v-if="errors.no_of_guests">
                                        {{ errors.no_of_guests[0] }}
                                    </small>
                                </div>

                                <div class="form-col-12 sm:form-col-6">
                                    <label for="reservation_date_time" class="db-field-title required">{{ $t("label.reservation_date_time") }}</label>
                                    <Datepicker hideInputIcon autoApply v-model="form.reservation_date_time" :enableTimePicker="true"
                                        :is24="false" :monthChangeOnScroll="false" utc="false"
                                        :input-class-name="errors.reservation_date_time ? 'invalid' : ''">
                                        <template #am-pm-button="{ toggle, value }">
                                            <button @click="toggle">{{ value }}</button>
                                        </template>
                                    </Datepicker>
                                    <small class="db-field-alert" v-if="errors.reservation_date_time">{{ errors.reservation_date_time[0] }}</small>
                                </div>

                                <div class="col-12">
                                    <div class="flex flex-wrap gap-3">
                                        <button type="submit"
                                            class="w-full db-btn h-12 text-center capitalize font-medium rounded-3xl text-white bg-primary">
                                            <i class="lab lab-save"></i>
                                            <span>{{ $t("button.confirm_reservation") }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import BreadcrumbComponent from "../../admin/components/BreadcrumbComponent";
import LoadingComponent from "../../admin/components/LoadingComponent";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import Datepicker from "@vuepic/vue-datepicker";

export default {
    name: "ProfileComponent",
    components: {
        BreadcrumbComponent,
        LoadingComponent,
        Datepicker
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                name: "",
                email: "",
                phone: "",
                no_of_guests: "",
                reservation_date_time: "",
                country_code: ""
            },
            flag: "",
            errors: {},
        }
    },
    mounted() {
        const countryCode = this.$store.getters['frontendCountryCode/show'];
        this.form.country_code = countryCode.calling_code
    },
    computed: {
        countryCode: function () {
            return this.$store.getters['frontendCountryCode/show'];
        }
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("frontendAddReservation/addReservation", this.form).then((res) => {
                    alertService.successFlip(0, this.$t("menu.reservation"));
                    this.loading.isActive = false;
                    this.$router.push({name: "frontend.home"});
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    },
    watch: {
        countryCode: {
            deep: true,
            handler(country) {
                this.flag = country.flag_emoji;
                this.form.country_code = country.calling_code;
            }
        }
    }
}
</script>
