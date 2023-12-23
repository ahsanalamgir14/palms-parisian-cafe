<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.reserve_a_table') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div class="dropdown-list db-card-filter-dropdown-list">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-filter-div">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="name" class="db-field-title after:hidden">{{ $t('label.name') }}</label>
                            <input id="name" v-model="props.search.name" type="text"
                                class="db-field-control">
                        </div>
                        
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="phone" class="db-field-title after:hidden">{{ $t('label.phone') }}</label>
                            <input id="phone" v-model="props.search.phone" type="text"
                                class="db-field-control">
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="email" class="db-field-title after:hidden">{{ $t('label.email') }}</label>
                            <input id="email" v-model="props.search.email" type="text"
                                class="db-field-control">
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStartDate" class="db-field-title after:hidden">
                                {{ $t('label.reservation_date_time') }}
                            </label>
                            <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false"
                                @update:modelValue="handleDate" v-model="props.form.reservation_date_time" range
                                :preset-ranges="presetRanges">
                                <template #yearly="{ label, range, presetDateRange }">
                                    <span @click="presetDateRange(range)">{{ label }}</span>
                                </template>
                            </Datepicker>
                        </div>

                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-search-line lab-font-size-16"></i>
                                    <span>{{ $t('button.search') }}</span>
                                </button>
                                <button class="db-btn py-2 text-white bg-gray-600" @click="clear">
                                    <i class="lab lab-cross-line-2 lab-font-size-22"></i>
                                    <span>{{ $t('button.clear') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.name') }}</th>
                            <th class="db-table-head-th">{{ $t('label.phone') }}</th>
                            <th class="db-table-head-th">{{ $t('label.email') }}</th>
                            <th class="db-table-head-th">{{ $t('label.no_of_guests') }}</th>
                            <th class="db-table-head-th">{{ $t('label.reservation_date_time') }}</th>
                            <!-- <th class="db-table-head-th hidden-print" v-if="permissionChecker('reservations')">
                                {{ $t('label.action') }}
                            </th> -->
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="reservations.length > 0">
                        <tr class="db-table-body-tr" v-for="reservation in reservations" :key="reservation">
                            <td class="db-table-body-td">
                                {{ reservation.name }}

                            </td>
                            <td class="db-table-body-td">
                                {{ reservation.phone }}
                            </td>

                            <td class="db-table-body-td">
                                {{ reservation.email }}
                            </td>
                            <td class="db-table-body-td">{{ reservation.no_of_guests }}</td>
                            <td class="db-table-body-td">
                                {{ reservation.reservation_date_time }}
                            </td>
                            <!-- <td class="db-table-body-td hidden-print" v-if="permissionChecker('reservations')">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                    <SmIconViewComponent :link="'admin.reservation.show'" :id="reservation.id"
                                        v-if="permissionChecker('reservations')" />
                                </div>
                            </td> -->
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }" />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import appService from "../../../services/appService";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import TableLimitComponent from "../components/TableLimitComponent";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import PrintComponent from "../components/buttons/export/PrintComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { ref } from 'vue';
import { endOfMonth, endOfYear, startOfMonth, startOfYear, subMonths } from 'date-fns';
import statusEnum from "../../../enums/modules/statusEnum";
import isAdvanceOrderEnum from "../../../enums/modules/isAdvanceOrderEnum";

export default {
    name: "OnlineOrderListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        LoadingComponent,
        SmIconDeleteComponent,
        SmIconViewComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent,
        Datepicker
    },
    setup() {
        const date = ref();

        const presetRanges = ref([
            { label: 'Today', range: [new Date(), new Date()] },
            { label: 'This month', range: [startOfMonth(new Date()), endOfMonth(new Date())] },
            {
                label: 'Last month',
                range: [startOfMonth(subMonths(new Date(), 1)), endOfMonth(subMonths(new Date(), 1))],
            },
            { label: 'This year', range: [startOfYear(new Date()), endOfYear(new Date())] },
            {
                label: 'This year (slot)',
                range: [startOfYear(new Date()), endOfYear(new Date())],
                slot: 'yearly',
            },
        ]);

        return {
            date,
            presetRanges,
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                orderStatusEnum: orderStatusEnum,
                orderTypeEnum: orderTypeEnum,
                isAdvanceOrderEnum: isAdvanceOrderEnum,
                // orderStatusEnumArray: {
                //     [orderStatusEnum.PENDING]: this.$t("label.pending"),
                //     [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                //     [orderStatusEnum.PROCESSING]: this.$t("label.processing"),
                //     [orderStatusEnum.OUT_FOR_DELIVERY]: this.$t("label.out_for_delivery"),
                //     [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                //     [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                //     [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                //     [orderStatusEnum.RETURNED]: this.$t("label.returned")
                // },
                // orderTypeEnumArray: {
                //     [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                //     [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway")
                // }
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t("menu.online_orders"),
            },
            props: {
                form: {
                    reservation_date_time: null,
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_by: "desc",
                    name: "",
                    from_date: "",
                    to_date: "",
                }
            }
        }
    },
    mounted() {
        this.list();
        this.$store.dispatch('user/lists', {
            order_column: 'id',
            order_type: 'asc',
        });
    },
    computed: {
        reservations: function () {
            return this.$store.getters['reservations/lists'];
        },
        customers: function () {
            return this.$store.getters['user/lists'];
        },
        pagination: function () {
            return this.$store.getters['reservations/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['reservations/page'];
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        search: function () {
            this.list();
        },
        handleDate: function (e) {
            if (e) {
                this.props.search.from_date = e[0];
                this.props.search.to_date = e[1];
            } else {
                this.props.form.date = null;
                this.props.search.from_date = null;
                this.props.search.to_date = null;
            }
        },
        clear: function () {
            this.props.search.paginate = 1;
            this.props.search.page = 1;
            this.props.search.order_by = "desc";
            this.props.search.name = "";
            this.props.search.email = "";
            this.props.search.phone = "";
            this.props.search.reservation_date_time = "";
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('reservations/lists', this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        xls: function () {
            this.loading.isActive = true;
            this.$store.dispatch("reservations/export", this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.reservations");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
    }
}
</script>

<style scoped>
@media print {
    .hidden-print {
        display: none !important;
    }
}
</style>
