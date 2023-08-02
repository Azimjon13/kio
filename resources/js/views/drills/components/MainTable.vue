<template>
    <div class="app-container">

        
        <SvodTable :itogData="itogForSvod" :date="date" :id_place="id_place"></SvodTable>
        


        <h3>Данные Буровых станков
            <el-button v-show="!tableConfigurations.show" type="primary" plain style="float: right;" @click="tableConfigurations.show=true;"> Показать настройки таблицы </el-button>            
            <el-button v-show="tableConfigurations.show" type="primary" style="float: right;" @click="tableConfigurations.show=false;">Скрыть настройки таблицы</el-button>
            <el-button type="success" style="float: right; margin-right: 10px;"  @click="showExcelConfigDialog">Excel</el-button>
            <el-button type="primary" style="float: right; margin-right: 10px;"  @click="openReportsList" v-if="checkPermission(['view reports'])">Отчёты</el-button>

        </h3>
        <div style="margin: 10px; padding: 10px; text-align: right;" v-show="tableConfigurations.show">
            <div style="display: inline; float: left; margin: 10px 0px;">
                <span style="float: left;">Показывать данные только за: </span>
                <br/>
                <el-radio v-model="tableConfigurations.datas" :label="1" border>Все</el-radio>
                <el-radio v-model="tableConfigurations.datas" :label="2" border>Только за сутки</el-radio>
                <el-radio v-model="tableConfigurations.datas" :label="3" border>Только с нач. месяца</el-radio>
            </div>
            <el-button 
                type="info" 
                style="margin-right: 20px;"
                @click="changeVolumeView"
                :plain="!tableConfigurations.volumes"
            >{{ tableConfigurations.volumes ?   "Показать объём" : "Не показывать объём" }}</el-button>
            <el-button 
                type="info"
                @click="changeDowntimeView"
                :plain="tableConfigurations.downtimesShort"
            >{{ tableConfigurations.downtimesShort ? "Показать простои сокращённо" : "Показать простои детально"}}</el-button>
        </div>

        <el-table
            :data="drillsData"
            fit  highlight-current-row border style="width: 100%;" align="center"
            ref="drillsDatas"
            :key="key"
            class="mainTable"
            max-height="850"
        >
            <el-table-column label="№ п/п" fixed align="center" width="50">
                <template slot-scope="scope">
                    <span v-if="!scope.row.itSummary">
                        {{ scope.$index+1 }}
                    </span>
                </template>
            </el-table-column>
            <el-table-column label="Буровые станки" prop="title"  fixed align="center" min-width="160px">
                <template slot-scope="scope">
                    <div class="drillDowntimesClass" v-if="!scope.row.itSummary" @click="handleShowDrillDowntimesDialog(scope.row.id_place_drill)">
                        {{ scope.row.title }}
                    </div>
                    <div v-else>{{ scope.row.title }}</div>
                </template>
            </el-table-column>
            <el-table-column label="Гараж №" prop="garage"   fixed align="center" width="75"></el-table-column>
            
            <el-table-column label="Бурение (п.м.)" align="center" v-if="tableConfigurations.volumes">
                <el-table-column label="Сутки" align="center" v-if="tableConfigurations.volumes && (tableConfigurations.datas == 1 || tableConfigurations.datas == 2)">
                    <el-table-column label="план" prop="plan" align="center" width="95">
                        <template slot-scope="scope">
                            <el-input 
                                v-model="scope.row.plan" 
                                v-if="(tableConfigurations.datas == 1 || 
                                        tableConfigurations.datas == 2) && 
                                        checkPermission(['manage drills']) && 
                                        !scope.row.itSummary"
                                @keyup.enter.native="savePlan(scope.row)"
                                @focus="saveFocus(scope.row, 'plan', scope.row.plan)"
                                @focusout.native="forgetValue(scope.row, 'plan')"
                            ></el-input>
                            <span v-else>{{ (+scope.row.plan).toLocaleString() }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="факт" prop="fact" align="center" width="95">
                        <template slot-scope="scope">
                            <el-input 
                                v-model="scope.row.fact" 
                                v-if="(tableConfigurations.datas == 1 || 
                                        tableConfigurations.datas == 2) && 
                                        checkPermission(['manage drills']) && 
                                        !scope.row.itSummary"
                                @keyup.enter.native="saveFact(scope.row)"
                                @focus="saveFocus(scope.row, 'fact', scope.row.fact)"
                                @focusout.native="forgetValue(scope.row, 'fact')"
                            ></el-input>
                            <span v-else>{{ (+scope.row.fact).toLocaleString() }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="+/-" align="center" prop="differenceDay">
                        <template slot-scope="scope">
                            <span :style="{color: (scope.row.differenceDay < 0 ? 'red': 'black') }"> {{ (+scope.row.differenceDay).toLocaleString() }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="%" align="center" width="65" prop="percentDay"></el-table-column>
                </el-table-column>
                <el-table-column label="C начала месяца" align="center" v-if="tableConfigurations.volumes && (tableConfigurations.datas == 1 || tableConfigurations.datas == 3)">
                    <el-table-column label="план" prop="plan_month" align="center">
                        <template slot-scope="scope">
                            {{ (+scope.row.plan_month).toLocaleString() }}
                        </template>
                    </el-table-column>
                    <el-table-column label="факт" prop="fact_month" align="center">
                        <template slot-scope="scope">
                            {{ (+scope.row.fact_month).toLocaleString() }}
                        </template>
                    </el-table-column>
                    <el-table-column label="+/-" align="center" prop="differenceMonth">
                        <template slot-scope="scope">
                            <span :style="{color: (scope.row.differenceMonth < 0 ? 'red': 'black') }"> {{ (+scope.row.differenceMonth).toLocaleString()  }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="%" align="center" width="65" prop="percentMonth"></el-table-column>
                </el-table-column>
            </el-table-column>

         
            <el-table-column label="Простои, час" align="center">
                <el-table-column label="Плановые" align="center">
                    <el-table-column 
                        v-for="reason in reasonsColumns.planned"
                        :label="reason.label"
                        align="center"
                        v-if="!tableConfigurations.downtimesShort"
                    >
                        <template slot="header" slot-scope="scope">
                            <span class="showByReasonsHeader" @click="handleShowReasonDowntimesDialog(reason)">{{ reason.label }}</span>
                        </template>
                        <el-table-column
                            label="Сутки"
                            :prop="'downtimes.'+reason.key+'.dayDowntime'"
                            align="center"
                            v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 2"
                        >
                        </el-table-column>
                        <el-table-column
                            label="с нач. месяца"
                            :prop="'downtimes.'+reason.key+'.sinceDowntime'"
                            align="center"
                            v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 3"
                        >
                        </el-table-column>
                    </el-table-column>
                    <el-table-column 
                        label="Всего"
                        align="center"
                    >
                        <el-table-column
                            label="Сутки"
                            :prop="'downtimes.'+reasonsColumns.plannedItog.key+'.dayDowntime'"
                            align="center"
                            class-name="plannedItog"
                            v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 2"
                        >
                        </el-table-column>
                        <el-table-column
                            label="с нач. месяца"
                            :prop="'downtimes.'+reasonsColumns.plannedItog.key+'.sinceDowntime'"
                            align="center"
                            class-name="plannedItog"
                            v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 3"
                        >
                        </el-table-column>
                    </el-table-column>
                </el-table-column>
                <el-table-column label="Внеплановые" align="center">
                    <el-table-column 
                        v-for="reason in reasonsColumns.unscheduled"
                        :label="reason.label"
                        align="center"
                        v-if="!tableConfigurations.downtimesShort"
                    >
                        <template slot="header" slot-scope="scope">
                            <span class="showByReasonsHeader" @click="handleShowReasonDowntimesDialog(reason)">{{ reason.label }}</span>
                        </template>
                        <el-table-column
                            label="Сутки"
                            :prop="'downtimes.'+reason.key+'.dayDowntime'"
                            align="center"
                            v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 2"
                        >
                        </el-table-column>
                        <el-table-column
                            label="с нач. месяца"
                            :prop="'downtimes.'+reason.key+'.sinceDowntime'"
                            align="center"
                            v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 3"
                        >
                        </el-table-column>
                    </el-table-column>
                    <el-table-column 
                        label="Всего"
                        align="center"
                    >
                        <el-table-column
                            label="Сутки"
                            :prop="'downtimes.'+reasonsColumns.unscheduledItog.key+'.dayDowntime'"
                            align="center"
                            class-name="unscheduledItog"
                            v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 2"
                        >
                        </el-table-column>
                        <el-table-column
                            label="с нач. месяца"
                            :prop="'downtimes.'+reasonsColumns.unscheduledItog.key+'.sinceDowntime'"
                            align="center"
                            class-name="unscheduledItog"
                            v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 3"
                        >
                        </el-table-column>
                    </el-table-column>
                </el-table-column>
                <el-table-column label="Всего" align="center">
                    <el-table-column 
                        label="Сутки"
                        :prop="'downtimes.'+reasonsColumns.summary.key+'.dayDowntime'"
                        align="center"
                        class-name="summaryDowntimes"
                        v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 2"
                    >
                    </el-table-column>
                    <el-table-column 
                        label="с нач. месяца"
                        :prop="'downtimes.'+reasonsColumns.summary.key+'.sinceDowntime'"
                        align="center"
                        class-name="summaryDowntimes"
                        v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 3"
                    >
                    </el-table-column>
                </el-table-column>
            </el-table-column>

            <el-table-column label="Время работы" align="center">
                <el-table-column 
                    label="Сутки" 
                    prop="workHour.day" 
                    align="center"
                    v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 2"
                >
                </el-table-column>
                <el-table-column 
                    label="с нач. месяца" 
                    prop="workHour.since" 
                    align="center"
                    v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 3"
                >
                </el-table-column>
            </el-table-column>
            <el-table-column label="КИО" align="center"  fixed="right"   width="160" >
                <el-table-column 
                    label="Сутки" 
                    prop="kio.day" 
                    align="center" 
                    v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 2"
                    :width="tableConfigurations.datas == 1 ? '80' : '160'"
                >
                </el-table-column>
                <el-table-column 
                    label="с нач. месяца" 
                    prop="kio.since" 
                    align="center"
                    v-if="tableConfigurations.datas == 1 || tableConfigurations.datas == 3"
                    :width="tableConfigurations.datas == 1 ? '80' : '160'"
                >
                </el-table-column>
            </el-table-column>
        </el-table>

        <el-dialog title="Настройка экспорта в Excel" width="40%" :visible.sync="excelConfig.show" :before-close="hideExcelConfigDialog">
            <div class="form-container">
                <el-form ref="exportExcelForm" :model="excelConfig" label-position="top" >
                    <el-descriptions :column="1" border >
                        <el-descriptions-item >
                            <template slot="label">
                                Показ бурения (п.м.)
                            </template>
                            <div style="text-align: center;">
                                <el-switch
                                    style="display: block"
                                    v-model="excelConfig.volumes"
                                    :active-value="true"
                                    :inactive-value="false"
                                    active-color="#13ce66"
                                    inactive-color="#ff4949"
                                    active-text="Да"
                                    inactive-text="Нет">
                                </el-switch>
                            </div>
                       </el-descriptions-item>
                       <el-descriptions-item >
                            <template slot="label">
                                Сокращённый показ причин
                            </template>
                            <div style="text-align: center;">
                                <el-switch
                                    style="display: block"
                                    v-model="excelConfig.downtimesShort"
                                    :active-value="true"
                                    :inactive-value="false"
                                    active-color="#13ce66"
                                    inactive-color="#ff4949"
                                    active-text="Сокращённо"
                                    inactive-text="Детально">
                                </el-switch>
                            </div>
                       </el-descriptions-item>
                      
                        <el-descriptions-item >
                            <template slot="label">
                               Показать данные за:
                            </template>
                            <div style="text-align: center;">
                                <el-radio-group v-model="excelConfig.datas">
                                    <el-radio-button :label="1">Все</el-radio-button>
                                    <el-radio-button :label="2">Только за Сутки</el-radio-button>
                                    <el-radio-button :label="3">Только с нач. месяца</el-radio-button>
                                </el-radio-group>
                            </div>
                       </el-descriptions-item>
                    </el-descriptions>
                </el-form>
                <br>
                <span slot="footer" class="dialog-footer" style="float:right;">
                    <el-button type="primary" @click="exportExcel">
                        Экспорт
                    </el-button>
                    <el-button type="danger" @click="hideExcelConfigDialog">
                        {{ $t('table.cancel') }}
                    </el-button>
                </span>
                <br><br>
            </div>
        </el-dialog>
        <DrillDowntimes 
            v-if="currentSelectedIdPlaceDrill" 
            :show="showDrillDowntimesDialog" 
            :close="handleCloseDrillDowntimesDialog"
            :id_place_drill="currentSelectedIdPlaceDrill"
            :date="date"
        ></DrillDowntimes>
        <DowntimesByReason
            v-if="currentSelectedIdReason"
            :show="showReasonDowntimesDialog"
            :close="handleCloseReasonDowntimesDialog"
            :id_place="id_place"
            :date="date"
            :id_reason="currentSelectedIdReason"
            :reasonDowntimesItog = "reasonDowntimesItog"
        ></DowntimesByReason>
        <PlaceReportsList :routerLink="'drillsReport'" :show="showReportsList" :close="closeReportsList" :id_technica_type = "1" :id_place="id_place" :date="date"></PlaceReportsList>

    </div>
</template>


<script>
import {getMainData, storePlan, storeFact} from "@/api/drills";
import {getPlaceForExcel} from "@/api/places";
import checkPermission from '@/utils/permission'; // Permission checking
import DrillDowntimes from "./DrillDowntimes.vue";
import DowntimesByReason from "./DowntimesByReason.vue";
import PlaceReportsList from "../../catalogs/reports/components/PlaceReportsList.vue";
import SvodTable from "./SvodTable.vue";

export default { 
    components: { DrillDowntimes, DowntimesByReason, PlaceReportsList, SvodTable },
    props: {
        id_place: Number,
        date: String,
        updater: Number,
    },
    watch: {
        date: function (newVal, oldVal) {
            this.loadData();
        }, 
        id_place: function(newVal, oldVal) 
        {
            this.loadData();
        }, 
        updater: function(newVal, oldVal) 
        {
            this.loadData();
        }
    }, 
    computed: {
        itogForSvod: function() 
        {
            return this.drillsData.at(-1);
        }
    },
    mounted() {
        this.loadData();
    },
    data() {
        return {

            //Список отчётов
            showReportsList:false,

            // Простои по одному конкретному буровому станку
            currentSelectedIdPlaceDrill: null, 
            showDrillDowntimesDialog: false,

            // Простои по одному конкретному Виду Простоя
            currentSelectedIdReason: null, 
            showReasonDowntimesDialog: false,
            reasonDowntimesItog: {
                day: 0,
                since: 0,
            },

            savedFocusValue: null, // Сохранения значения при фокусировке ввода на плане или факте, чтобы потом заменить на старую в случае чего
            
            reasonsList: [], // список Видов простоев
            drillsData: [], // Главный список по причинам
            reasonsColumns: {}, 

            excelConfig: {
                show: false,
                volumes: true, 
                downtimesShort: true,
                datas: 1, // 1 - all, 2 - only dayliy, 3 - only since
            },

            tableConfigurations: {
                show:false,
                volumes: true,
                downtimesShort: true,
                datas: 1, // all
            },
            mainItog: {},

            key: this.id_place+1,
        }
    }, 
    methods: {
        checkPermission,
        async loadData() 
        {
            // Закачка данных
            const res = await getMainData(this.date, this.id_place );
            // засекаем время использованное для пересчёта данных
            console.time();
            /*
                res === {
                    plans_facts: [], <-- массив объектов по каждому буровому станку и его плану и факту
                    downtimes: []  <-- массив объектов с причинами 
                                        могут быть дубликаты т.к. по разным буровым станкам могут встречаться
                                        одинаковые причины простоев, поэтому чуть ниже мы убираем дубликаты
                }
            */


            // получаем чистый(без дубликатов) список простоев
            const reasons = res.data.downtimes; 
            this.reasonsList = reasons.filter((value,index, self) => 
                index === self.findIndex(t => 
                (
                    t.id_reason === value.id_reason
                ))
            ); 

           
            // подготавливаем отчёт из планов и фактов добавляем туда разницу и проценты
            this.drillsData = res.data.plans_facts.map(e => ({
                differenceDay: Math.round((e.fact-e.plan)*10)/10,  // +/- за день
                differenceMonth: Math.round((e.fact_month-e.plan_month)*10)/10, // +/- за месяц
                differenceYear: Math.round((e.fact_year-e.plan_year)*10)/10, // +/- за год
                percentDay: e.plan  && e.fact ? Math.round((e.fact/e.plan)*100*10)/10 : "", // % за день
                percentMonth: e.plan_month  && e.fact_month ? Math.round((e.fact_month/e.plan_month)*100*10)/10 : "", // % за месяц
                percentYear: e.plan_year  && e.fact_year ? Math.round((e.fact_year/e.plan_year)*100*10)/10 : "",  // % за год
                plan: Math.round(e.plan*10)/10,
                fact: Math.round(e.fact*10)/10,
                plan_month: Math.round(e.plan_month*10)/10,
                fact_month: Math.round(e.fact_month*10)/10,
                plan_year: Math.round(e.plan_year*10)/10,
                fact_year: Math.round(e.fact_year*10)/10,
                
                ...e,  // тут получаем всё остальное (план и факт, данные бурового станка название гаражный и т.д.)

                // далее мы составляем для данного эксаватора часы простоев по спискам простоев
                downtimes: this.reasonsList.reduce((acc, cur, i) => {
                    // сначала ищем по Буровые станки и По текущему простою есть ли данные
                    let a = res.data.downtimes.find(d => (
                        d.id_place_drill == e.id_place_drill && 
                        d.id_drill == e.id_drill && 
                        d.id_reason == cur.id_reason
                    ));
                    // если данные то записываем эти данные
                    if(a) 
                    {
                        acc[cur.id_reason] = { 
                            ...a, 
                            dayDowntime: Math.round(a.dayDowntime*100)/100,
                            sinceDowntime: Math.round(a.sinceDowntime*100)/100,
                        };
                    }
                    // иначе если данных нет, то мы записываем пустой Простой без часов простоев
                    else 
                        acc[cur.id_reason]= {
                            ...cur,
                            id_place_drill: e.id_place_drill, 
                            id_drill: e.id_drill, 
                            dayDowntime: null, 
                            sinceDowntime: null, 
                        };
                    return acc;
                }, {}),
            }));


            const mainItogDowntimes = {
                planned: {
                    dayDowntime: 0,
                    sinceDowntime: 0
                },
                unscheduled: {
                    dayDowntime: 0,
                    sinceDowntime: 0
                },
                summary: {
                    dayDowntime: 0,
                    sinceDowntime: 0
                }
            };
            
            const mainData = res.data.main;
            const mainItog = {
                    itSummary: true,
                    plan: 0,
                    fact: 0,
                    differenceDay: 0,
                    percentDay: 0,

                    plan_month: +mainData.plans_facts.plan_month,
                    fact_month: +mainData.plans_facts.fact_month,
                    differenceMonth: 0,
                    percentMonth: 0,

                    plan_year: +mainData.plans_facts.plan_year,
                    fact_year: +mainData.plans_facts.fact_year,
                    differenceYear: 0,
                    percentYear: 0,
                    title: "Всего по карьеру",
                
                    downtimes: { 
                        ...this.reasonsList.reduce((acc, cur, i) => {
                            let dt = res.data.downtimes.filter(f => f.id_reason === cur.id_reason);

                            acc[cur.id_reason] = {
                                ...cur,
                                dayDowntime: Math.round((dt.reduce((p, c) => p+ +c.dayDowntime, 0))*100)/100, 
                                sinceDowntime: Math.round((+mainData.downtimes.find(f => f.id_reason == cur.id_reason).excludedDowntime)*100)/100,
                            }

                            return acc;
                        }, {}),
                    },
                    workHour: {
                        day: 0,
                        since: 0
                    },
                    kio: {
                        day: 1,
                        since: 1
                    }
            };

        
            res.data.plans_facts.forEach(e => {
                mainItog.plan += +e.plan;
                mainItog.fact += +e.fact;
                mainItog.plan_month += +e.plan_month;
                mainItog.fact_month += +e.fact_month;
                mainItog.plan_year += +e.plan_year;
                mainItog.fact_year += +e.fact_year;
            });
            mainItog.differenceDay = Math.round((mainItog.fact-mainItog.plan)*10)/10;  // +/- за день
            mainItog.differenceMonth = Math.round((mainItog.fact_month-mainItog.plan_month)*10)/10; // +/- за месяц
            mainItog.differenceYear = Math.round((mainItog.fact_year-mainItog.plan_year)*10)/10; // +/- за год
            mainItog.percentDay = mainItog.plan  && mainItog.fact ? Math.round((mainItog.fact/mainItog.plan)*100*10)/10 : ""; // % за день
            mainItog.percentMonth = mainItog.plan_month  && mainItog.fact_month ? Math.round((mainItog.fact_month/mainItog.plan_month)*100*10)/10 : ""; // % за месяц
            mainItog.percentYear = mainItog.plan_year  && mainItog.fact_year ? Math.round((mainItog.fact_year/mainItog.plan_year)*100*10)/10 : "";  // % за год

            mainItog.plan = Math.round(mainItog.plan*10)/10;
            mainItog.fact = Math.round(mainItog.fact*10)/10;
            mainItog.plan_month = Math.round(mainItog.plan_month*10)/10;
            mainItog.fact_month = Math.round(mainItog.fact_month*10)/10;
            mainItog.plan_year = Math.round(mainItog.plan_year*10)/10;
            mainItog.fact_year = Math.round(mainItog.fact_year*10)/10;



            // пересчитываем всё для итоговых Плановых, Внеплановых и Эксплуатаций вне объёма, а так же для итогового
            this.drillsData = this.drillsData.map(e => {
                
                const planned = {
                    dayDowntime: 0, 
                    sinceDowntime: 0,
                };
                const unscheduled = {
                    dayDowntime: 0, 
                    sinceDowntime: 0,
                };
                const summary = { 
                    dayDowntime: 0, 
                    sinceDowntime: 0,
                };
                const keysOfDowntimes = Object.keys(e.downtimes);
                let downtimesR = e.downtimes;

                
                // в цикле получаем все причины простоев  для текущего бурового станка 
                keysOfDowntimes.forEach(d => {

                    if(downtimesR[d].type == 1) // если тип плановый то записываем к плановому
                    {
                        planned.dayDowntime += +downtimesR[d].dayDowntime;
                        planned.sinceDowntime += +downtimesR[d].sinceDowntime;
                    }
                    else if(downtimesR[d].type == 2)// если тип внеплановый то записываем к внеплановому
                    {
                        unscheduled.dayDowntime += +downtimesR[d].dayDowntime;
                        unscheduled.sinceDowntime += +downtimesR[d].sinceDowntime;
                    }
                    
                });



                const hours = new Date(this.date).getDate() * 24; // Вычисляем сколько должен был работать 1 эксаватор с начала месяца
                const x = 0.66666667; // Регламентированный простой 
        
                let summaryDay = planned.dayDowntime+unscheduled.dayDowntime; 
                let summarySince = planned.sinceDowntime+unscheduled.sinceDowntime; 



                if(summaryDay <= 6 )
                    planned.dayDowntime += x*2;
                else if(summaryDay <= 12)
                    planned.dayDowntime += x; 
        
        
                let coef = Math.floor((hours-summarySince)/12); 
                planned.sinceDowntime += coef*x;


                mainItogDowntimes.planned.dayDowntime += planned.dayDowntime;
                mainItogDowntimes.planned.sinceDowntime += planned.sinceDowntime;
                mainItogDowntimes.unscheduled.dayDowntime += unscheduled.dayDowntime; 
                mainItogDowntimes.unscheduled.sinceDowntime += unscheduled.sinceDowntime;

        


                planned.dayDowntime = Math.round(planned.dayDowntime*100)/100;
                planned.sinceDowntime = Math.round(planned.sinceDowntime*100)/100; 
 
                
                unscheduled.dayDowntime = Math.round(unscheduled.dayDowntime*100)/100;
                unscheduled.sinceDowntime = Math.round(unscheduled.sinceDowntime*100)/100;

                summary.dayDowntime = Math.round((planned.dayDowntime + unscheduled.dayDowntime)*100)/100; // Итоговый вычисляем за Сутки
                summary.sinceDowntime = Math.round((planned.sinceDowntime + unscheduled.sinceDowntime)*100)/100; // Итоговый вычисляем С нач месяца

    
                mainItogDowntimes.summary.dayDowntime += summary.dayDowntime;
                mainItogDowntimes.summary.sinceDowntime += summary.sinceDowntime;
                
                const workHour = {  
                    day: Math.round((24-summary.dayDowntime)*100)/100,  // и вычисляем сколько он за сегодня отработал
                    since: Math.round((hours - summary.sinceDowntime)*100)/100 // вычисляем сколько отработал с начала месяца
                };


                // Вычисляем кио для эксаватора
                const kio = { 
                    day: Math.round((workHour.day*100)/24)/100, 
                    since: Math.round((workHour.since*100)/hours)/100, 
                };

                
                return {
                    ...e, 
                    downtimes: {
                        ...e.downtimes, 
                        planned,
                        unscheduled,
                        summary
                    },
                    workHour,
                    kio
                }
               
                
            });
            

            mainItogDowntimes.planned.dayDowntime = Math.round(mainItogDowntimes.planned.dayDowntime*100)/100;
            mainItogDowntimes.planned.sinceDowntime = Math.round(mainItogDowntimes.planned.sinceDowntime*100)/100;
            mainItogDowntimes.unscheduled.dayDowntime = Math.round(mainItogDowntimes.unscheduled.dayDowntime*100)/100;
            mainItogDowntimes.unscheduled.sinceDowntime = Math.round(mainItogDowntimes.unscheduled.sinceDowntime*100)/100;
            mainItogDowntimes.summary.dayDowntime = Math.round(mainItogDowntimes.summary.dayDowntime*100)/100;
            mainItogDowntimes.summary.sinceDowntime = Math.round(mainItogDowntimes.summary.sinceDowntime*100)/100;
            

            mainItog.downtimes = {...mainItog.downtimes, ...mainItogDowntimes};
            
            const dayHours = 24*this.drillsData.length;
            const sinceHours = (new Date(this.date).getDate())*dayHours + +mainData.hours.addingHours;
            mainItog.workHour = {
                day: Math.round((dayHours - mainItog.downtimes.summary.dayDowntime)*100)/100,
                since: Math.round((sinceHours - mainItog.downtimes.summary.sinceDowntime)*100)/100,
            }
            mainItog.kio = { 
                day: Math.round((mainItog.workHour.day*100)/dayHours)/100, 
                since: Math.round((mainItog.workHour.since*100)/sinceHours)/100
            };


            // this.mainItog = mainItog;
            this.drillsData.push(mainItog);

            this.reasonsColumns = {
              planned: this.reasonsList.filter(r => r.type == 1).map(r=> ({
                label: r.reason, 
                key: r.id_reason,
              })),
              plannedItog: {
                label:"Всего",
                key:"planned"
              }, 
              unscheduled: this.reasonsList.filter(r => r.type == 2).map(r=> ({
                label: r.reason, 
                key: r.id_reason,
              })),
              unscheduledItog: {
                label:"Всего",
                key:"unscheduled"
              }, 
              summary: {
                label:"ИТОГО",
                key:"summary",
              }
            };
            console.timeEnd();
            
        }, 
        
        changeVolumeView()
        {
            this.tableConfigurations.volumes = !this.tableConfigurations.volumes;
            this.$nextTick(() => { this.$refs['drillsDatas'].doLayout(); })
        }, 
        changeDowntimeView() 
        {
            this.tableConfigurations.downtimesShort = !this.tableConfigurations.downtimesShort;
            this.$nextTick(() => { this.$refs['drillsDatas'].doLayout(); })

        }, 

        saveFocus(row, type, value)
        {
            this.savedFocusValue = {
                id_place_drill: row.id_place_drill, 
                id_drill: row.id_drill, 
                type: type, 
                value: value,
            }
            console.log(value);
        },
        forgetValue(row, type) {
            if(row.id_place_drill == this.savedFocusValue.id_place_drill && type == this.savedFocusValue.type)
            {
                row[type] = this.savedFocusValue.value;
            }
        },
        async savePlan(row) 
        {
            if(row.plan != this.savedFocusValue.value && this.savedFocusValue.type == 'plan' && !isNaN(row.plan))
            {
                this.savedFocusValue.value = row.plan;

                const data = {
                    id_place: this.id_place,
                    id_place_drill: row.id_place_drill, 
                    id_drill: row.id_drill, 
                    plan: row.plan, 
                    date_plan: this.date,
                };
                const res = await storePlan(data);
                if(!res.errors && res.status) 
                {
                    await this.loadData();
                    this.$message({
                        message:`План успешно сохранён!`,
                        type:"success",
                        duration: 5*1000
                    });
                }
                else 
                {
                    this.$message({
                        message:"При сохранении плана произошла ошибка!",
                        type:'error',
                        duration: 5*1000,
                    });
                }
            }
        },
        async saveFact(row)
        {
            if(row.fact != this.savedFocusValue.value && this.savedFocusValue.type == 'fact' && !isNaN(row.fact))
            {
                this.savedFocusValue.value = row.fact;

                const data = {
                    id_place: this.id_place,
                    id_place_drill: row.id_place_drill, 
                    id_drill: row.id_drill, 
                    fact: row.fact, 
                    date_fact: this.date,
                };
                const res = await storeFact(data);
                if(!res.errors && res.status) 
                {
                    await this.loadData();
                    this.$message({
                        message:`Факт успешно сохранён!`,
                        type:"success",
                        duration: 5*1000
                    });
                }
                else 
                {
                    this.$message({
                        message:"При сохранении факта произошла ошибка!",
                        type:'error',
                        duration: 5*1000,
                    });
                }
            }
        }, 
        
        handleShowDrillDowntimesDialog(id_place_drill) 
        {
            console.log(id_place_drill);
            this.currentSelectedIdPlaceDrill = id_place_drill;
            this.showDrillDowntimesDialog = true;
        },
        handleCloseDrillDowntimesDialog()
        {
            this.showDrillDowntimesDialog = false;
            this.currentSelectedIdPlaceDrill = null;
        },


        handleShowReasonDowntimesDialog(reason)
        {
            
            const day = this.drillsData.at(-1).downtimes[reason.key].dayDowntime;
            const since = this.drillsData.at(-1).downtimes[reason.key].sinceDowntime;

            this.reasonDowntimesItog = {
                day, 
                since,
            };
            
            this.currentSelectedIdReason = reason.key; 
            this.showReasonDowntimesDialog = true;
         
        },
        handleCloseReasonDowntimesDialog()
        {
            this.currentSelectedIdReason = null; 
            this.showReasonDowntimesDialog = false;
        },

        // excel 

        showExcelConfigDialog()
        {
            this.excelConfig = {
                show: true,
                volumes: true, 
                downtimesShort: true,
                datas: 1, // 1 - all, 2 - only dayliy, 3 - only since
            };
        },
        hideExcelConfigDialog() {
            this.excelConfig.show = false;
        },

        exportExcel() 
        {
            
            import("@/vendor/Export2Excel").then(async (excel) => {
                const headers = await this.excelHeader();
                const data = this.excelGetData();

                excel.export_json_to_excel({
                   multiHeader: headers.multiHeader,
                   header: headers.header,
                   data,
                   merges: headers.merges,
                });
                
            });
        }, 
        async excelHeader() 
        {
            const res = await getPlaceForExcel(this.id_place);

            const columns = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                             'AA', 'AB', 'AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                             'BA', 'BB', 'BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                            ];
           
            const merges = [];
            const multiHeader = [];
            const firstRow = ['№ п/п', 'Буровые станки', 'Гараж №'];
            const secondRow = ['','',''];
            const thirdRow = ['','',''];
            const fourthRow = ['','',''];
            if(this.excelConfig.volumes)
            {
                firstRow.push(`Объём`);
                if(this.excelConfig.datas==1) { 
                    firstRow.push('','','','','','',''); // 7 клеток пропускаем

                    secondRow.push(`Сутки`, '','','', `С начала месяца`,'','','');
                    thirdRow.push(  `план`, `факт`, `+/-`, `%`,  // Сутки
                                    `план`, `факт`, `+/-`, `%`,  // С начала месяца
                                );
                    fourthRow.push(``, ``, ``, ``, ``, ``, ``, ``); // 8 клеток пропускаем
                }
                else {   
                    firstRow.push('','',''); 
                    secondRow.push(`план`, `факт`, `+/-`, `%`); // Сутки OR С начала месяца
                    thirdRow.push(``, ``, ``, ``);
                    fourthRow.push(``, ``, ``, ``);
                }
            }
          
         
            if(!this.excelConfig.downtimesShort) // if not short 
            {
                let planned = this.reasonsList.filter(r => r.type == 1).map(r => r.reason);
                planned.push(`Всего`);

                let unscheduled = this.reasonsList.filter(r => r.type == 2).map(r=>r.reason);
                unscheduled.push(`Всего`);
                
                if(this.excelConfig.datas == 1)
                {
                    firstRow.push(`Простои`);
                    secondRow.push(`Плановые`);
                    for(let i = 0; i<planned.length; i++)
                    {
                        if(i == planned.length-1)
                        {
                            firstRow.push(``);
                            secondRow.push(``);
                        }
                        else 
                        {
                            firstRow.push(``, ``);
                            secondRow.push(``, ``);
                        }
                        thirdRow.push(planned[i], '');
                        fourthRow.push(`Сутки`, `С нач. месяца`);
                    }

                    secondRow.push(`Внеплановые`);
                    for(let i = 0; i<unscheduled.length; i++)
                    {
                       
                        if(i == planned.length - 1)
                        {
                            firstRow.push('');
                            secondRow.push('');
                        }
                        else 
                        {
                            firstRow.push('','');
                            secondRow.push('','');
                        }
                        thirdRow.push(unscheduled[i],'');
                        fourthRow.push(`Сутки`, `С нач. месяца`);
                    }
                    //          1 недостающий + 2 для Итого
                    firstRow.push('', '', '');
                    secondRow.push('ИТОГО', '');
                    thirdRow.push('Сутки', `С нач. месяца`);
                    fourthRow.push(``, ``);
                }
                else {
                    firstRow.push(`Простои`);
                    secondRow.push(`Плановые`);
                    for(let i = 0; i<planned.length; i++)
                    {
                        firstRow.push(``);
                        secondRow.push(``);
                        thirdRow.push(planned[i]);
                        fourthRow.push(``);
                    }
                    secondRow.pop();

                    secondRow.push(`Внеплановые`);
                    for(let i = 0; i<unscheduled.length; i++)
                    {
                        firstRow.push(``);
                        secondRow.push(``);
                        thirdRow.push(unscheduled[i]);
                        fourthRow.push(``);
                    }
                    secondRow.pop();

                    secondRow.push(`Итого`);
                    thirdRow.push('');
                    fourthRow.push('');
                }
            }
            else if(this.excelConfig.downtimesShort && this.excelConfig.datas == 1) 
            {
                firstRow.push('Простои', '','','','','');
                secondRow.push(`Плановые`, '', 'Внеплановые', '', 'Итого', '');
                thirdRow.push(
                                `Сутки`, `С нач. месяца`,  // Плановые
                                `Сутки`, `С нач. месяца`,  // Внеплановые
                                `Сутки`, `С нач. месяца`,  // Всего
                            );
                fourthRow.push('','','','','','');
            }
            else {
                firstRow.push('Простои', '', '');
                secondRow.push('Плановые', 'Внеплановые', 'ИТОГО');
                thirdRow.push('', '','');
                fourthRow.push('','','');
            }

            
            if(this.excelConfig.datas == 1)
            {
                firstRow.push('Время работы', '', 'КИО', ''); 

                secondRow.push(
                                `Сутки`, `С нач. месяца`, // Время
                                `Сутки`, `С нач. месяца`
                            );
                thirdRow.push('', '','','');
                fourthRow.push('','','','');
            }
            else 
            {
                firstRow.push(`Время работы`, `КИО`);
                secondRow.push('','');
                thirdRow.push('','');
                fourthRow.push('','');
            }

            
            multiHeader.push(firstRow);

        
            if(this.excelConfig.datas == 1 && !this.excelConfig.downtimesShort)
            {
                merges.push(`A2:A5`, `B2:B5`, `C2:C5`);
                multiHeader.push(secondRow, thirdRow, fourthRow);
            }
            else if(this.excelConfig.datas != 1 && this.excelConfig.downtimesShort) 
            {
                multiHeader.push(secondRow);
                merges.push(`A2:A3`, `B2:B3`, `C2:C3`);
            }
            else 
            {
                multiHeader.push(secondRow, thirdRow);
                merges.push(`A2:A4`, `B2:B4`, `C2:C4`);
            }
            
            let columnCounter = {
                firstRow: 3,
                secondRow: 3,
                thirdRow: multiHeader.length <= 2 ? 0 : 3,
                fourthRow: multiHeader.length == 3 ?  3 : 0,
            };

            
            // MERGES  
            if(this.excelConfig.volumes)
            {
                if(this.excelConfig.datas == 1) // если требуется полные данные (сутки, с нач месяца)
                {
                    columnCounter.firstRow += 8;
                    columnCounter.secondRow += 8;

                    merges.push(`D2:K2`,  // Объём на 8 клеток
                                `D3:G3`,  // Объём за сутки на 4 клеток
                                `H3:K3`   // Объём с начала месяца на 4 клеток
                                );
                    if(!this.excelConfig.downtimesShort)
                    {
                        columnCounter.thirdRow  +=8;
                        columnCounter.fourthRow += 8;
                        merges.push(`D4:D5`, `E4:E5`, `F4:F5`, `G4:G5`, `H4:H5`,`I4:I5`,`J4:J5`,`K4:K5`); // на 2 строки
                    }
                }
                else {
                    columnCounter.firstRow += 4;
                    columnCounter.secondRow += 4;
                    columnCounter.thirdRow += 4;
                    merges.push(`D2:G2`); // Объём на 4 клеток 
                    if(!this.excelConfig.downtimesShort)
                    {
                        columnCounter.fourthRow += 4;
                        merges.push(`D3:D4`, `E3:E4`, `F3:F4`, `G3:G4`); // на 2 строки
                    }
                }
            }
            
         
            

            let plannedSize  = this.reasonsList.filter(r=>r.type == 1).length;
            let unscheduledSize = this.reasonsList.filter(r=>r.type==2).length;
                

            if(this.excelConfig.datas == 1 && !this.excelConfig.downtimesShort)
            {
                let sizeReasons = (plannedSize + unscheduledSize)*2 + 6; // + 2 плановые всего, +2 внеплановые всего, +2 всего
                let nameReasonColumnStart = columns[columnCounter.firstRow];
                let nameReasonColumnEnd = columns[columnCounter.firstRow+sizeReasons-1];
                merges.push(`${nameReasonColumnStart}2:${nameReasonColumnEnd}2`);

                columnCounter.firstRow += sizeReasons;

                let namePlannedColumnStart = columns[columnCounter.secondRow];
                let namePlannedColumnEnd = columns[columnCounter.secondRow+plannedSize*2+2-1];
                merges.push(`${namePlannedColumnStart}3:${namePlannedColumnEnd}3`);

                columnCounter.secondRow += plannedSize*2+2;

                for(let i = columnCounter.thirdRow; i<columnCounter.secondRow; i+=2)
                {
                    let cnStart = columns[i];
                    let cnEnd = columns[i+1];
                    merges.push(`${cnStart}4:${cnEnd}4`);
                    columnCounter.fourthRow+=2;
                }
                columnCounter.thirdRow += plannedSize*2+2;

                let nameUnscheduledColumnStart = columns[columnCounter.secondRow];
                let nameUnscheduledColumnEnd = columns[columnCounter.secondRow + unscheduledSize*2+2-1];
                merges.push(`${nameUnscheduledColumnStart}3:${nameUnscheduledColumnEnd}3`);
                
                columnCounter.secondRow += unscheduledSize*2+2;
                for(let i = columnCounter.thirdRow; i<columnCounter.secondRow; i+=2)
                {
                    let cnStart = columns[i];
                    let cnEnd = columns[i+1];
                    merges.push(`${cnStart}4:${cnEnd}4`);
                    columnCounter.fourthRow+=2;
                }
                columnCounter.thirdRow += unscheduledSize*2+2;
                
                let nameItogStart = columns[columnCounter.secondRow];
                let nameItogEnd = columns[columnCounter.secondRow+1];
                merges.push(`${nameItogStart}3:${nameItogEnd}3`);
                columnCounter.secondRow += 2;

                merges.push(`${nameItogStart}4:${nameItogStart}5`); // Сутки
                merges.push(`${nameItogEnd}4:${nameItogEnd}5`); // с нач. месяца
                columnCounter.thirdRow += 2;
                columnCounter.fourthRow += 2;
            
                let workHourCNStart = columns[columnCounter.firstRow];
                let workHourCNEnd = columns[columnCounter.firstRow+1];
                columnCounter.firstRow += 2;

                merges.push(`${workHourCNStart}2:${workHourCNEnd}2`);

                merges.push(`${workHourCNStart}3:${workHourCNStart}5`);
                merges.push(`${workHourCNEnd}3:${workHourCNEnd}5`);
                columnCounter.secondRow += 2;
                columnCounter.thirdRow += 2;
                columnCounter.fourthRow += 2;

                let kioCNStart = columns[columnCounter.firstRow];
                let kioCNEnd = columns[columnCounter.firstRow+1];
                columnCounter.firstRow += 2;

                merges.push(`${kioCNStart}2:${kioCNEnd}2`);

                merges.push(`${kioCNStart}3:${kioCNStart}5`);
                merges.push(`${kioCNEnd}3:${kioCNEnd}5`);
                columnCounter.secondRow += 2;
                columnCounter.thirdRow += 2;
                columnCounter.fourthRow += 2;
            }
            else if(this.excelConfig.datas == 1 && this.excelConfig.downtimesShort)
            {
                let sizeReasons = 6; // + 2 плановые всего, +2 внеплановые всего, +2 всего
                let nameReasonColumnStart = columns[columnCounter.firstRow];
                let nameReasonColumnEnd = columns[columnCounter.firstRow+sizeReasons-1];
                merges.push(`${nameReasonColumnStart}2:${nameReasonColumnEnd}2`);

                columnCounter.firstRow += sizeReasons;

                let namePlannedColumnStart = columns[columnCounter.secondRow];
                let namePlannedColumnEnd = columns[columnCounter.secondRow+1];
                merges.push(`${namePlannedColumnStart}3:${namePlannedColumnEnd}3`);

                columnCounter.secondRow += 2;
                columnCounter.thirdRow += 2;
                
                let nameUnscheduledColumnStart = columns[columnCounter.secondRow];
                let nameUnscheduledColumnEnd = columns[columnCounter.secondRow+1];
                merges.push(`${nameUnscheduledColumnStart}3:${nameUnscheduledColumnEnd}3`);
                
                columnCounter.secondRow += 2;
                columnCounter.thirdRow += 2;

                let nameItogStart = columns[columnCounter.secondRow];
                let nameItogEnd = columns[columnCounter.secondRow+1];
                merges.push(`${nameItogStart}3:${nameItogEnd}3`);
                columnCounter.secondRow += 2;
                columnCounter.thirdRow += 2;

                let workHourCNStart = columns[columnCounter.firstRow];
                let workHourCNEnd = columns[columnCounter.firstRow+1];
                columnCounter.firstRow += 2;

                merges.push(`${workHourCNStart}2:${workHourCNEnd}2`);

                merges.push(`${workHourCNStart}3:${workHourCNStart}4`);
                merges.push(`${workHourCNEnd}3:${workHourCNEnd}4`);
                columnCounter.secondRow += 2;
                columnCounter.thirdRow += 2;
                columnCounter.fourthRow += 2;

                let kioCNStart = columns[columnCounter.firstRow];
                let kioCNEnd = columns[columnCounter.firstRow+1];
                columnCounter.firstRow += 2;

                merges.push(`${kioCNStart}2:${kioCNEnd}2`);

                merges.push(`${kioCNStart}3:${kioCNStart}4`);
                merges.push(`${kioCNEnd}3:${kioCNEnd}4`);
                columnCounter.secondRow += 2;
                columnCounter.thirdRow += 2;
                columnCounter.fourthRow += 2;

            }
            else if(this.excelConfig.datas != 1 && !this.excelConfig.downtimesShort)
            {
                let sizeReasons  = plannedSize + unscheduledSize + 3;
                let nameReasonColumnStart = columns[columnCounter.firstRow];
                let nameReasonColumnEnd = columns[columnCounter.firstRow+sizeReasons-1];
                merges.push(`${nameReasonColumnStart}2:${nameReasonColumnEnd}2`);

                columnCounter.firstRow += sizeReasons;

                let namePlannedColumnStart = columns[columnCounter.secondRow];
                let namePlannedColumnEnd = columns[columnCounter.secondRow+plannedSize];
                merges.push(`${namePlannedColumnStart}3:${namePlannedColumnEnd}3`);

                columnCounter.secondRow += plannedSize+1;
                columnCounter.thirdRow += plannedSize+1;
                

                let nameUnscheduledColumnStart = columns[columnCounter.secondRow];
                let nameUnscheduledColumnEnd = columns[columnCounter.secondRow + unscheduledSize];
                merges.push(`${nameUnscheduledColumnStart}3:${nameUnscheduledColumnEnd}3`);
                
                columnCounter.secondRow += unscheduledSize+1;
                columnCounter.thirdRow += unscheduledSize+1;
              
                let nameItogStart = columns[columnCounter.secondRow];
                merges.push(`${nameItogStart}3:${nameItogStart}4`);
                columnCounter.secondRow += 1;
                columnCounter.thirdRow += 1;
                


                let workHourCNStart = columns[columnCounter.firstRow];
                columnCounter.firstRow += 1;
                columnCounter.secondRow += 1;
                columnCounter.thirdRow += 1;
                merges.push(`${workHourCNStart}2:${workHourCNStart}4`);
                

                let kioCNStart = columns[columnCounter.firstRow];
                columnCounter.firstRow += 1;
                columnCounter.secondRow += 1;
                columnCounter.thirdRow += 1;
                merges.push(`${kioCNStart}2:${kioCNStart}4`);
            }
            else 
            {
                let sizeReasons = 3; 
                let nameReasonColumnStart = columns[columnCounter.firstRow];
                let nameReasonColumnEnd = columns[columnCounter.firstRow+sizeReasons-1];
                merges.push(`${nameReasonColumnStart}2:${nameReasonColumnEnd}2`);
                
                columnCounter.firstRow += sizeReasons;

                let workHourCNStart = columns[columnCounter.firstRow];
          
                merges.push(`${workHourCNStart}2:${workHourCNStart}3`);
                columnCounter.firstRow += 1;

                let kioCNStart = columns[columnCounter.firstRow];
                columnCounter.firstRow += 1;
                merges.push(`${kioCNStart}2:${kioCNStart}3`);
            }

            let placeName = res.data.place_title;
            let parentName = res.data.parent_title;
            let managerName = res.data.manager_title;
            let title = `Оперативные производственные показатели буровых станков `;
           
            title += placeName + ' ';
            if(parentName) 
            {
                title += parentName + " ";
            }
            title += `(${managerName}) `;

            if(this.excelConfig.datas == 1)
            {
                let d = new Date(this.date).toLocaleDateString();
                title += ` на момент ${d} г.`;     
            }
            else if(this.excelConfig.datas == 2) 
            {
                let d = new Date(this.date).toLocaleDateString();
                title += ` за сутки на ${d} `;
            }
            else 
            {
                const months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
                const m = new Date(this.date).getMonth();
                const y = new Date(this.date).getFullYear();
                const countDaysInMonth = new Date(y, m+1, 0).getDate();
                const thisDay = new Date(this.date).getDate();

                let monthTitle = months[new Date(this.date).getMonth()];
                if(thisDay == countDaysInMonth) 
                {
                    title += ` за ${monthTitle} месяц ${new Date(this.date).getFullYear()} г.`;
                }
                else 
                {
                    title += ` с начала месяца по ${new Date(this.date).toLocaleDateString()} г.`;
                }
            }

            const titleRow = [title];
            const titleRowLastColumnName = columns[columnCounter.firstRow-1];

            merges.push(`A1:${titleRowLastColumnName}1`);
            const data = ['ТЕСТ'];
            for(let i = 0; i < firstRow.length-1;  i++)
            {
                titleRow.push('');
                data.push('');
            }
            multiHeader.unshift(titleRow);
            return {
                header: multiHeader.pop(),
                multiHeader,
                merges, 
                columnCounter, 
                fourthRow, 
                data
            };
        }, 
        excelGetData() {
            const data = [];
            this.drillsData.forEach((e, i, self ) => {

                const currentDrill = [i+1, e.title, e.garage ?? ''];
                if(i == self.length - 1) 
                    currentDrill[0] = '';

                if(this.excelConfig.volumes) 
                {
                    if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1) 
                        currentDrill.push(e.plan, e.fact, e.differenceDay, e.percentDay)
                    if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1) 
                        currentDrill.push(e.plan_month, e.fact_month, e.differenceMonth, e.percentMonth);
                }

                
                if(!this.excelConfig.downtimesShort)
                {
                    
                    const plannedIds = this.reasonsList.filter(r => r.type == 1).map(r=>r.id_reason);
                    const planned = Object.keys(e.downtimes).filter(r => plannedIds.includes(+r) || r == 'planned');

                    const unscheduledIds = this.reasonsList.filter(r => r.type == 2).map(r => r.id_reason);
                    const unscheduled = Object.keys(e.downtimes).filter(r => unscheduledIds.includes(+r) || r == 'unscheduled');

                    planned.forEach(r => { 
                        if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1)  
                            currentDrill.push(e.downtimes[r].dayDowntime);
                        if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1) 
                            currentDrill.push(e.downtimes[r].sinceDowntime);
                    });

                    unscheduled.forEach(r => { 
                        if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1)  
                            currentDrill.push(e.downtimes[r].dayDowntime);
                        if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1) 
                            currentDrill.push(e.downtimes[r].sinceDowntime);
                    });
                    
                    if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1)
                        currentDrill.push(e.downtimes['summary'].dayDowntime);
                    if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1)
                        currentDrill.push(e.downtimes['summary'].sinceDowntime);

                }
                else 
                {
                    if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1) 
                        currentDrill.push(e.downtimes['planned'].dayDowntime);
                    if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1)
                        currentDrill.push(e.downtimes['planned'].sinceDowntime);

                    if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1) 
                        currentDrill.push(e.downtimes['unscheduled'].dayDowntime);
                    if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1)
                        currentDrill.push(e.downtimes['unscheduled'].sinceDowntime);

                    if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1) 
                        currentDrill.push(e.downtimes['summary'].dayDowntime);
                    if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1)
                        currentDrill.push(e.downtimes['summary'].sinceDowntime);
                }
               
                if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1) 
                    currentDrill.push(e.workHour.day);
                if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1) 
                    currentDrill.push(e.workHour.since);
                if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1) 
                    currentDrill.push(e.kio.day);
                if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1) 
                    currentDrill.push(e.kio.since);

                data.push(currentDrill);
            });
            return data;
        },

        openReportsList()
        {
            this.showReportsList = true;
        },
        closeReportsList() 
        {
            this.showReportsList = false;
        }
    }
    
}
</script>

<style>

.unscheduledItog { 
    background-color: #ffcece;
}
.plannedItog { 
    background-color: #e1ffbd;
}
.summaryDowntimes{
    background-color: #fff99c;
}
.el-table__cell .cell {
  word-break: break-word !important;
}
.el-table { 
    font-size: 13px;
}
.cell{
    color: black;
}

.mainTable tbody tr:last-child { 
    font-weight: bold;
}
.drillDowntimesClass:hover { 
    cursor: pointer;
    text-decoration: underline;
}

.showByReasonsHeader:hover { 
    cursor: pointer;
    text-decoration: underline;
}
</style>