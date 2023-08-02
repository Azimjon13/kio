<template>
    <div class="app-container">

        <SvodTable :itogData="itogForSvod" :date="date" :id_place="id_place"></SvodTable>
    
        <h3>Данные ДСМ
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
                @click="changeDowntimeView"
                :plain="tableConfigurations.downtimesShort"
            >{{ tableConfigurations.downtimesShort ? "Показать простои сокращённо" : "Показать простои детально"}}</el-button>
        </div>

        <el-table
            :data="dsmsData"
            fit  highlight-current-row border style="width: 100%;" align="center"
            ref="dsmsDatas"
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
            <el-table-column label="ДСМ" prop="title"  fixed align="center" min-width="160px">
                <template slot-scope="scope">
                    <div class="dsmDowntimesClass" v-if="!scope.row.itSummary" @click="handleShowDsmDowntimesDialog(scope.row.id_place_dsm)">
                        {{ scope.row.title }}
                    </div>
                    <div v-else>{{ scope.row.title }}</div>
                </template>
            </el-table-column>
            <el-table-column label="Гараж №" prop="garage"   fixed align="center" width="75"></el-table-column>
            
         
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
        <DsmDowntimes 
            v-if="currentSelectedIdPlaceDsm" 
            :show="showDsmDowntimesDialog" 
            :close="handleCloseDsmDowntimesDialog"
            :id_place_dsm="currentSelectedIdPlaceDsm"
            :date="date"
        ></DsmDowntimes>
        <DowntimesByReason
            v-if="currentSelectedIdReason"
            :show="showReasonDowntimesDialog"
            :close="handleCloseReasonDowntimesDialog"
            :id_place="id_place"
            :date="date"
            :id_reason="currentSelectedIdReason"
            :reasonDowntimesItog = "reasonDowntimesItog"
        ></DowntimesByReason>
        <PlaceReportsList :routerLink="'dsmsReport'" :show="showReportsList" :close="closeReportsList" :id_technica_type = "1" :id_place="id_place" :date="date"></PlaceReportsList>

    </div>
</template>


<script>
import {getMainData, storePlan, storeFact} from "@/api/dsms";
import {getPlaceForExcel} from "@/api/places";
import checkPermission from '@/utils/permission'; // Permission checking
import DsmDowntimes from "./DsmDowntimes.vue";
import DowntimesByReason from "./DowntimesByReason.vue";
import PlaceReportsList from "../../catalogs/reports/components/PlaceReportsList.vue";
import SvodTable from "./SvodTable.vue";

export default { 
    components: { DsmDowntimes, DowntimesByReason, PlaceReportsList, SvodTable },
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
            return this.dsmsData.at(-1);
        }
    },
    mounted() {
        this.loadData();
    },
    data() {
        return {

            //Список отчётов
            showReportsList:false,

            // Простои по одному конкретному ДСМ
            currentSelectedIdPlaceDsm: null, 
            showDsmDowntimesDialog: false,

            // Простои по одному конкретному Виду Простоя
            currentSelectedIdReason: null, 
            showReasonDowntimesDialog: false,
            reasonDowntimesItog: {
                day: 0,
                since: 0,
            },

            savedFocusValue: null, // Сохранения значения при фокусировке ввода на плане или факте, чтобы потом заменить на старую в случае чего
            
            reasonsList: [], // список Видов простоев
            dsmsData: [], // Главный список по причинам
            reasonsColumns: {}, 

            excelConfig: {
                show: false,
                downtimesShort: true,
                datas: 1, // 1 - all, 2 - only dayliy, 3 - only since
            },

            tableConfigurations: {
                show:false,
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
                    plans_facts: [], <-- массив объектов по каждому ДСМ и его плану и факту
                    downtimes: []  <-- массив объектов с причинами 
                                        могут быть дубликаты т.к. по разным ДСМ могут встречаться
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
            this.dsmsData = res.data.plans_facts.map(e => ({
                ...e,  // тут получаем всё остальное (план и факт, данные ДСМ название гаражный и т.д.)
                // далее мы составляем для данного эксаватора часы простоев по спискам простоев
                downtimes: this.reasonsList.reduce((acc, cur, i) => {
                    // сначала ищем по ДСМ и По текущему простою есть ли данные
                    let a = res.data.downtimes.find(d => (
                        d.id_place_dsm == e.id_place_dsm && 
                        d.id_dsm == e.id_dsm && 
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
                            id_place_dsm: e.id_place_dsm, 
                            id_dsm: e.id_dsm, 
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

            // пересчитываем всё для итоговых Плановых, Внеплановых и Эксплуатаций вне объёма, а так же для итогового
            this.dsmsData = this.dsmsData.map(e => {
                
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

                // в цикле получаем все причины простоев  для текущего ДСМ
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

                const hours = new Date(this.date).getDate() * 24; // Вычисляем сколько должен был работать 1 ДСМ с начала месяца
                const x = 1.9; // Регламентированный простой 
    
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
            
            const dayHours = 24*this.dsmsData.length;
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
            this.dsmsData.push(mainItog);

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
        
        changeDowntimeView() 
        {
            this.tableConfigurations.downtimesShort = !this.tableConfigurations.downtimesShort;
            this.$nextTick(() => { this.$refs['dsmsDatas'].doLayout(); })

        }, 

        handleShowDsmDowntimesDialog(id_place_dsm) 
        {
            console.log(id_place_dsm);
            this.currentSelectedIdPlaceDsm = id_place_dsm;
            this.showDsmDowntimesDialog = true;
        },

        handleCloseDsmDowntimesDialog()
        {
            this.showDsmDowntimesDialog = false;
            this.currentSelectedIdPlaceDsm = null;
        },

        handleShowReasonDowntimesDialog(reason)
        {
            const day = this.dsmsData.at(-1).downtimes[reason.key].dayDowntime;
            const since = this.dsmsData.at(-1).downtimes[reason.key].sinceDowntime;

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
            const firstRow = ['№ п/п', 'ДСМ', 'Гараж №'];
            const secondRow = ['','',''];
            const thirdRow = ['','',''];
            const fourthRow = ['','',''];
           
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
            let title = `Оперативные производственные показатели ДСМ `;
           
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
            this.dsmsData.forEach((e, i, self ) => {

                const currentDsm = [i+1, e.title, e.garage ?? ''];
                if(i == self.length - 1) 
                    currentDsm[0] = '';

                if(!this.excelConfig.downtimesShort)
                {
                    const plannedIds = this.reasonsList.filter(r => r.type == 1).map(r=>r.id_reason);
                    const planned = Object.keys(e.downtimes).filter(r => plannedIds.includes(+r) || r == 'planned');

                    const unscheduledIds = this.reasonsList.filter(r => r.type == 2).map(r => r.id_reason);
                    const unscheduled = Object.keys(e.downtimes).filter(r => unscheduledIds.includes(+r) || r == 'unscheduled');

                    planned.forEach(r => { 
                        if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1)  
                            currentDsm.push(e.downtimes[r].dayDowntime);
                        if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1) 
                            currentDsm.push(e.downtimes[r].sinceDowntime);
                    });

                    unscheduled.forEach(r => { 
                        if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1)  
                            currentDsm.push(e.downtimes[r].dayDowntime);
                        if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1) 
                            currentDsm.push(e.downtimes[r].sinceDowntime);
                    });
                    
                    if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1)
                        currentDsm.push(e.downtimes['summary'].dayDowntime);
                    if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1)
                        currentDsm.push(e.downtimes['summary'].sinceDowntime);

                }
                else 
                {
                    if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1) 
                        currentDsm.push(e.downtimes['planned'].dayDowntime);
                    if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1)
                        currentDsm.push(e.downtimes['planned'].sinceDowntime);

                    if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1) 
                        currentDsm.push(e.downtimes['unscheduled'].dayDowntime);
                    if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1)
                        currentDsm.push(e.downtimes['unscheduled'].sinceDowntime);

                    if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1) 
                        currentDsm.push(e.downtimes['summary'].dayDowntime);
                    if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1)
                        currentDsm.push(e.downtimes['summary'].sinceDowntime);
                }
               
                if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1) 
                    currentDsm.push(e.workHour.day);
                if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1) 
                    currentDsm.push(e.workHour.since);
                if(this.excelConfig.datas == 2 || this.excelConfig.datas == 1) 
                    currentDsm.push(e.kio.day);
                if(this.excelConfig.datas == 3 || this.excelConfig.datas == 1) 
                    currentDsm.push(e.kio.since);

                data.push(currentDsm);
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
.dsmDowntimesClass:hover { 
    cursor: pointer;
    text-decoration: underline;
}

.showByReasonsHeader:hover { 
    cursor: pointer;
    text-decoration: underline;
}
</style>