<template>
    <div class="app-container">
        <h3>
            {{ reportInfo.description }}.
            <br/>
            По площадке: {{ reportInfo.place }}.
            <br/>
            Для вида техники: {{ reportInfo.technica_type }}.
            <span style="float: right;">
                на дату:
                <el-date-picker
                    v-model="date"
                    value-format="yyyy-MM-dd"
                    type="date"
                    format="dd.MM.yyyy"
                    :clearable="false"

                    @change="dateChanged"
                    :picker-options="{
                        shortcuts: [{
                                        text: 'Сегодня',
                                        onClick(picker) {
                                        picker.$emit('pick', new Date());
                                        }
                                    }]
                    }"
                ></el-date-picker>
            </span>
        </h3>

        <br>

        <el-button type="primary" @click="openCodes"> Используемые коды</el-button>
        <el-button type="success" @click="exportExcel">Excel</el-button>
        <el-table
            :data="locomotiveDatasByReport"
            fit  highlight-current-row border style="width: 100%;" align="center"
            ref="locomotivesDatasByReport"
            class="MainTableByReport"
            max-height="850"
        >
            <el-table-column label="№ п/п"  align="center" width="50">
                <template slot-scope="scope">
                    <span v-if="!scope.row.itSummary">
                        {{ scope.$index+1 }}
                    </span>
                </template>
            </el-table-column>
            <el-table-column label="Тепловоз" prop="title"   align="center" min-width="160px">
                <template slot-scope="scope">
                    <div class="locomotiveDowntimesClass" v-if="!scope.row.itSummary">
                        {{ scope.row.title }}
                    </div>
                    <div v-else>{{ scope.row.title }}</div>
                </template>
            </el-table-column>
            <el-table-column label="Серийный №" prop="serial_number"  align="center" width="75"></el-table-column>

            <el-table-column
                v-for="reason in filteredReasonsList"
                :label="reason.reason"
                align="center"
            >
                <el-table-column label="Сутки" :prop="'downtimes.'+reason.id_reason+'.dayDowntime'" align="center"></el-table-column>
                <el-table-column label="с нач. месяца" :prop="'downtimes.'+reason.id_reason+'.sinceDowntime'" align="center"></el-table-column>
            </el-table-column>

            <el-table-column label="Время работы" align="center">
                <el-table-column 
                    label="Сутки" 
                    prop="workHour.day" 
                    align="center"
                >
                </el-table-column>
                <el-table-column 
                    label="с нач. месяца" 
                    prop="workHour.since" 
                    align="center"
                >
                </el-table-column>
            </el-table-column>
            <el-table-column label="КИО" align="center"  >
                <el-table-column 
                    label="Сутки" 
                    prop="kio.day" 
                    align="center" 
                >
                </el-table-column>
                <el-table-column 
                    label="с нач. месяца" 
                    prop="kio.since" 
                    align="center"
                >
                </el-table-column>
            </el-table-column>
        </el-table>
    
        <el-dialog title="Используемые коды в данном отчёте" width="60%" :visible.sync="showCodes" :before-close="closeCodes">
            <el-table
                :data="reportCodes"
                fit  highlight-current-row border style="width: 100%;" align="center"
                class="codesListReport"
            >
                <el-table-column label="№ п/п" :min-width="5">
                    <template slot-scope="scope">
                        {{ scope.$index + 1 }}
                    </template>
                </el-table-column>
                <el-table-column label="Тип причины"  :min-width="10">
                    <template slot-scope="scope">
                        {{ scope.row.type == 1 ? "Плановый" : (scope.row.type == 2 ? "Внеплановый" : "Эксп. вне объёма") }}
                    </template>
                </el-table-column>
                <el-table-column label="Вид причины" prop="reason" :min-width="20"></el-table-column>
                <el-table-column label="Код" prop="code" :min-width="6"></el-table-column>
                <el-table-column label="Описание кода" prop="title" :min-width="43"></el-table-column>
                <el-table-column label="Для типа Тепловоза" prop="title_technica_group" :min-width="16"></el-table-column>
            </el-table>
        </el-dialog>
        


    </div>
</template>


<script>
import {locomotiveDatasByReport} from "@/api/locomotives";
import {getReport} from "../../api/reports";
import {reasonsWithCodes} from "@/api/reasons";
import checkPermission from '@/utils/permission'; // Permission checking

export default { 

    watch: {
        date: function (newVal, oldVal) {
            this.loadData();
        }, 
    },
    data() 
    {
        return { 
            id_report: this.$route.params.id_report,
            date: sessionStorage.getItem('date') || this.getPreviousDate(),


            showCodes: false, 

            reasonsList: [], // список Видов простоев
            locomotivesData: [], // Главный список по причинам
            reasonsColumns: {}, 

            downtimesByReport: [],
            locomotivesDataByReport: [],
            mainDowntimesByReport: [],
            filteredReasonsList: [],
            reportInfo: {},

            reportCodes: [],
            
        }
    }, 
    created() {
      
        this.loadReportInfo();
        this.loadData();

    }, 
    methods : {
        checkPermission,
        async loadReportInfo() 
        {
            const codesRes = await reasonsWithCodes(`/locomotive`);
            const reportRes = await getReport(this.id_report);

            const reasons = codesRes.data; 
            let codes = reasons.flatMap(r => r.codes);
            let reportCodes = reportRes.data.downtime_codes.map(c => c.id);
            codes = codes.filter(c => reportCodes.includes(c.id));
            codes = codes.map(c => {
                const reason = reasons.find(r => r.id == c.id_reason); 
                return { 
                    ...c, 
                    reason: reason.reason, 
                    type: reason.type, 
                }
            });
            this.reportInfo = {
                description: reportRes.data.description, 
                place: reportRes.data.place, 
                technica_type: reportRes.data.technica_type
            };
            this.reportCodes = codes;
        

        },
        async loadData() 
        {
            const res = await locomotiveDatasByReport(this.id_report, this.date);

            this.downtimesByReport = res.data.downtimes_by_report;
            this.mainDowntimesByReport = res.data.main.downtimes_by_report;

            console.time();

            // получаем чистый(без дубликатов) список простоев
            const reasons = res.data.downtimes; 
            this.reasonsList = reasons.filter((value,index, self) => 
                index === self.findIndex(t => 
                (
                    t.id_reason === value.id_reason
                ))
            ); 

           
            // подготавливаем отчёт из планов и фактов добавляем туда разницу и проценты
            this.locomotivesData = res.data.plans_facts.map(e => ({
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
                
                ...e,  // тут получаем всё остальное (план и факт, данные Тепловоза название гаражный и т.д.)

                // далее мы составляем для данного эксаватора часы простоев по спискам простоев
                downtimes: this.reasonsList.reduce((acc, cur, i) => {
                    // сначала ищем по Тепловоза и По текущему простою есть ли данные
                    /////
                    let a = res.data.downtimes.find(d => (
                        d.id_place_locomotive == e.id_place_locomotive && 
                        d.id_locomotive == e.id_locomotive && 
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
                            id_place_locomotive: e.id_place_locomotive, 
                            id_locomotive: e.id_locomotive, 
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
                outScope: {
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
            this.locomotivesData = this.locomotivesData.map(e => {
                
                const planned = {
                    dayDowntime: 0, 
                    sinceDowntime: 0,
                };
                const unscheduled = {
                    dayDowntime: 0, 
                    sinceDowntime: 0,
                };
                const outScope = {
                    dayDowntime: 0, 
                    sinceDowntime: 0,
                };
                const summary = { 
                    dayDowntime: 0, 
                    sinceDowntime: 0,
                };
                const keysOfDowntimes = Object.keys(e.downtimes);
                let downtimesR = e.downtimes;

                
                // в цикле получаем все причины простоев  для текущего Тепловоза
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
                    else if(downtimesR[d].type == 3) // если тип эксп.вне объёма то записываем к эксп.вне объёма
                    {
                        outScope.dayDowntime += +downtimesR[d].dayDowntime;
                        outScope.sinceDowntime += +downtimesR[d].sinceDowntime;
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
                mainItogDowntimes.outScope.dayDowntime += outScope.dayDowntime;
                mainItogDowntimes.outScope.sinceDowntime += outScope.sinceDowntime; 
    

                planned.dayDowntime = Math.round(planned.dayDowntime*100)/100;
                planned.sinceDowntime = Math.round(planned.sinceDowntime*100)/100; 
 
                unscheduled.dayDowntime = Math.round(unscheduled.dayDowntime*100)/100;
                unscheduled.sinceDowntime = Math.round(unscheduled.sinceDowntime*100)/100;
                outScope.dayDowntime = Math.round(outScope.dayDowntime*100)/100;
                outScope.sinceDowntime = Math.round(outScope.sinceDowntime*100)/100;
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
                        outScope,
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
            mainItogDowntimes.outScope.dayDowntime = Math.round(mainItogDowntimes.outScope.dayDowntime*100)/100;
            mainItogDowntimes.outScope.sinceDowntime = Math.round(mainItogDowntimes.outScope.sinceDowntime*100)/100;
            mainItogDowntimes.summary.dayDowntime = Math.round(mainItogDowntimes.summary.dayDowntime*100)/100;
            mainItogDowntimes.summary.sinceDowntime = Math.round(mainItogDowntimes.summary.sinceDowntime*100)/100;
            

            mainItog.downtimes = {...mainItog.downtimes, ...mainItogDowntimes};
            
            const dayHours = 24*this.locomotivesData.length;
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
            this.locomotivesData.push(mainItog);

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
              outScope: this.reasonsList.filter(r => r.type == 3).map(r=> ({
                label: r.reason, 
                key: r.id_reason,
              })),
              outScopeItog: {
                label:"Всего",
                key:"outScope"
              },
              summary: {
                label:"ИТОГО",
                key:"summary",
              }
            };
            console.timeEnd();
            this.calculateDowntimes();
        }, 

        calculateDowntimes() 
        {
            const reportedReasonsList = this.downtimesByReport.reduce((acc, curr, ind) => {
                if(!acc.find(a => a.id_reason == curr.id_reason))
                {
                   acc.push({
                    id_reason: curr.id_reason, 
                    type: curr.type, 
                    reason: curr.reason, 
                    is_reported: curr.is_reported,
                   });
                }
                return acc;
            },[]);

            this.filteredReasonsList = reportedReasonsList;

            const mainDowntimes = reportedReasonsList.reduce((acc, curr,ind) => {
                acc[curr.id_reason] = { 
                    id_reason: curr.id_reason,
                    reason: curr.reason,
                    dayDowntime: 0, 
                    sinceDowntime: +this.mainDowntimesByReport.find(f => f.id_reason == curr.id_reason).excludedDowntime,
                };
                return acc;
            }, {});


            // console.log(this.locomotivesData);
            const locomotives = this.locomotivesData.map((e, i, s) => {
                if(s.length - 1 != i) 
                {
                    return { 
                        title: e.title, 
                        workHour: e.workHour, 
                        serial_number: e.serial_number,
                        kio: e.kio,
                        id_locomotive: e.id_locomotive,
                        downtimes: reportedReasonsList.reduce((acc, curr, i) => {
                            /////
                            let a = this.downtimesByReport.find(d => (
                                d.id_place_locomotive == e.id_place_locomotive && 
                                d.id_locomotive == e.id_locomotive && 
                                d.id_reason == curr.id_reason
                            ));
                            // если данные то записываем эти данные
                            if(a) 
                            {
                                acc[curr.id_reason] = { 
                                    ...a, 
                                    dayDowntime: Math.round(a.dayDowntime*100)/100,
                                    sinceDowntime: Math.round(a.sinceDowntime*100)/100,
                                };

                                mainDowntimes[curr.id_reason].dayDowntime += +a.dayDowntime;
                               // mainDowntimes[curr.id_reason].sinceDowntime += a.sinceDowntime; считается автоматом

                            }
                            // иначе если данных нет, то мы записываем пустой Простой без часов простоев
                            else 
                                acc[curr.id_reason]= {
                                    ...curr,
                                    id_place_locomotive: e.id_place_locomotive, 
                                    id_locomotive: e.id_locomotive, 
                                    dayDowntime: null, 
                                    sinceDowntime: null, 
                                };
                            return acc;
                        }, {})
                    }
                }
                else 
                {
                    return { 
                        title: e.title + ` в часах`, 
                        workHour: e.workHour, 
                        serial_number: '',
                        kio: e.kio, 
                        downtimes: mainDowntimes ,
                        itSummary: true,
                    }
                }
            });

            const linkItog = locomotives.at(-1).downtimes;
            Object.keys(linkItog).forEach(key => {
                linkItog[key].dayDowntime = Math.round(linkItog[key].dayDowntime*100)/100;
                linkItog[key].sinceDowntime = Math.round(linkItog[key].sinceDowntime*100)/100;
            });


            const percentage = {
                title:"Простои относительно %",
                workHour: {day: "", since: ""},
                kio: {day: "", since: ""},
                serial_number:"",
                itSummary: true,
                downtimes: reportedReasonsList.reduce((acc, curr, i ) => {
                    acc[curr.id_reason] = { 
                        dayDowntime: Math.round(((linkItog[curr.id_reason].dayDowntime/this.locomotivesData.at(-1).downtimes.summary.dayDowntime)*100)*10)/10,
                        sinceDowntime: Math.round(((linkItog[curr.id_reason].sinceDowntime/this.locomotivesData.at(-1).downtimes.summary.sinceDowntime)*100)*10)/10,
                    }
                    return acc;
                }, {})
            }



            locomotives.push(percentage);
            this.locomotiveDatasByReport = locomotives;
        },

        dateChanged(date)
        {
            sessionStorage.setItem(`date`, this.date);
        }, 
        getPreviousDate()
        {
            const previous = new Date();
            previous.setDate(previous.getDate() - 1);
            const d = new Date(previous).toISOString().slice(0,10);
            return d;
        },

        openCodes()
        {
            this.showCodes = true;
        },
        closeCodes() {
            this.showCodes = false;
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
           
            const columns = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                             'AA', 'AB', 'AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                             'BA', 'BB', 'BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                            ];
           
            const merges = [];
        
            const firstRow = ['№ п/п', 'Тепловоз', 'Серийный №'];
            const secondRow = ['','',''];
            let firstColumnCounter = 3;
            merges.push('A2:A3', 'B2:B3', 'C2:C3');

            this.filteredReasonsList.forEach((reason, index) => {
                firstRow.push(reason.reason, '');
                secondRow.push(`Сутки`, `С нач. месяца`);
                merges.push(`${columns[firstColumnCounter]}2:${columns[firstColumnCounter+1]}2`);
                firstColumnCounter += 2;
            });
            firstRow.push(`Время работы`, ``);
            secondRow.push(`Сутки`, `С нач. месяца`);
            merges.push(`${columns[firstColumnCounter]}2:${columns[firstColumnCounter+1]}2`);
            firstColumnCounter += 2;

            firstRow.push(`КИО`, ``);
            secondRow.push(`Сутки`, `С нач. месяца`);
            merges.push(`${columns[firstColumnCounter]}2:${columns[firstColumnCounter+1]}2`);
            firstColumnCounter += 2;
            

            const titleRow = [this.reportInfo.description + 
                                ". По площадке: " +  this.reportInfo.place + ". " + 
                                " Для вида техники: " + this.reportInfo.technica_type + 
                                " на дату: " + new Date(this.date).toLocaleDateString()];
            
            for(let i = 1; i < firstColumnCounter; i++) 
            {
                titleRow.push(``);
            }
            merges.push(`A1:${columns[firstColumnCounter-1]}1`);
            return {
                header: secondRow,
                multiHeader: [titleRow, firstRow],
                merges, 
            };
        }, 
        excelGetData() {
            const data = [];

            this.locomotiveDatasByReport.forEach((e, i, self) => {
                const currentLocomotive = [i+1, e.title, e.serial_number ?? ''];
                if(i >= self.length - 2)
                    currentLocomotive[0] = '';
                Object.keys(e.downtimes).forEach(key => {
                    currentLocomotive.push(e.downtimes[key].dayDowntime ? e.downtimes[key].dayDowntime : "0");
                    currentLocomotive.push(e.downtimes[key].sinceDowntime ? e.downtimes[key].sinceDowntime : "0");
                }); 
                currentLocomotive.push(e.workHour.day, e.workHour.since, e.kio.day, e.kio.since);
                data.push(currentLocomotive);
            });
          
            return data;
        },
        
    }


}

</script>

<style> 
.el-table__cell .cell {
  word-break: break-word !important;
}
.el-table { 
    font-size: 13px;
}

.cell{
    color: black;
}

.MainTableByReport tbody tr:last-child, .MainTableByReport tbody tr:nth-last-child(2) { 
    font-weight: bold;
}

</style>