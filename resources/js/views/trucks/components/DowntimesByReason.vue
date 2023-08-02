<template>
    <el-dialog 
        title="Данные простоев  с начала месяца" 
        width="70%" 
        :visible.sync="this.show" 
        :before-close="close"
    >
        <div class="form-container">
            <el-descriptions direction="vertical" :column="5" border>
                <el-descriptions-item label="Площадка">
                    {{place.place}}
                </el-descriptions-item>
                <el-descriptions-item label="Тип причины">
                    {{ place.type == 1 ? "Плановый":"Внеплановый"}}
                </el-descriptions-item>
                <el-descriptions-item label="Причина">
                    {{place.reason}}
                </el-descriptions-item>
                <el-descriptions-item label="На дату">
                    <span>С {{ new Date(shifts.shift_month_start).toLocaleString(undefined, {year:'numeric', month:'numeric', day:"numeric", hour:'2-digit', minute:'2-digit'}).replace(',', ' ') }}</span> 
                    <br>
                    <span>По {{ new Date(shifts.shift_end).toLocaleString(undefined, {year:'numeric', month:'numeric', day:"numeric", hour:'2-digit', minute:'2-digit'}).replace(',', ' ') }}</span>
                </el-descriptions-item>
                <el-descriptions-item label="Простои">
                    <span>За сутки: {{ reasonDowntimesItog.day }} </span> 
                    <br>
                    <span>С начала месяца: {{ reasonDowntimesItog.since }}</span>
                </el-descriptions-item>
            </el-descriptions>

            <br>

            
            <br>
            <el-select v-model="downtimesFilter.id_technica_group" @change="filterDowntimes('reset')">
                <el-option :value="null" label="Все типы"></el-option>
                <el-option v-for="group in truckGroupTypes" :value="group.id_technica_group" :label="group.title_technica_type"></el-option>
            </el-select>


            <el-select 
                    v-model="downtimesFilter.id_truck" 
                    @change="filterDowntimes"      
                    filterable 
                >
                <el-option class="truckOption all"
                    label="Все автосамосвалы"
                    :value="null"
                >
                    <div class="autocomplete-list-item all">
                        <span class="name-autocomplete">
                            Все автосамосвалы
                        </span>
                    </div>
                </el-option>
                <el-option class="truckOption"
                    v-for="truck in trucksListFiltered"
                    :label="truck.title_truck"
                    :value="truck.id_truck"
                >
                    <div class="autocomplete-list-item">
                            <span class="name-autocomplete">
                                <b>Наименование:</b> {{ truck.title_truck }}
                            </span>
                            <br>
                            <span class="garage-autocomplete">
                                <b>Гаражный №:</b>  {{ truck.garage }}
                            </span>
                        </div>
                </el-option>
            </el-select>

            

            <el-button type="success" @click="exportExcel" style="float:right;">Excel</el-button>
            <br>
            
            <el-table
                :data="downtimesFiltered"
                fit  highlight-current-row border style="width: 100%;" align="center"
                ref="downtimesByReason"
                class="downtimesByReasonTable"
                :summary-method="getSummaries"
                show-summary
            >
                <el-table-column label="№ п/п" :min-width="4" align="center">
                    <template slot-scope="scope">{{ scope.$index+1 }}</template>
                </el-table-column>
                <el-table-column label="Тип Автосамосвала" prop="title_technica_type" :min-width="9" align="center"></el-table-column>
                <el-table-column label="Автосамосвал" prop="title_truck" :min-width="15" align="center"></el-table-column>
                <el-table-column label="Гаражный №" prop="garage" :min-width="9" align="center"></el-table-column>
                <el-table-column label="Код" prop="code" :min-width="5" align="center"></el-table-column>
                <el-table-column label="Описание кода простоя" prop="title_code" :min-width="28" align="center"></el-table-column>
                <el-table-column label="Дата начала простоя" prop="start_time" :min-width="10" align="center">
                    <template slot-scope="scope">
                        {{ new Date(scope.row.start_time).toLocaleString(undefined, {year:"numeric", month:"numeric", day:"numeric", hour:"2-digit", minute:"2-digit"}).replace(',', ' ') }}
                    </template>
                </el-table-column>
                <el-table-column label="Дата окончания простоя" prop="end_time" :min-width="10" align="center">
                    <template slot-scope="scope">
                        {{ scope.row.end_time ? new Date(scope.row.end_time).toLocaleString(undefined, {year:"numeric", month:"numeric", day:"numeric", hour:"2-digit", minute:"2-digit"}).replace(',', ' ') : " не окончен "}}
                    </template>
                </el-table-column>
                <el-table-column label="Длительность (в час)" prop="sinceDuration" :min-width="10" align="center"></el-table-column>
            </el-table>
            <br>

            
        </div>
    </el-dialog>
</template>

<script>
// import {downtimesByReasonPlace} from "@/api/trucks";
import {downtimesByReasonPlace} from "../../../api/trucks";


export default {
  
    props: {
        show: Boolean,
        close: Function,
        
        id_reason: Number | String,
        id_place: Number, 
        date: String,
        reasonDowntimesItog: Object,
    },
    created() {
        this.loadData();
    },
    
    data() {
        return { 
       

            place: {
                place: "Карьер Балпантау",
                type: 1, 
                reason: "Регламентированные", 
            },
            shifts: {
                shift_month_start: new Date().toISOString(),
                shift_end: new Date().toISOString(),
            },

            downtimes: [],
            downtimesFiltered: [],

            trucksList: [],
            trucksListFiltered: [],
            truckGroupTypes: [],

            downtimesFilter : {
                id_technica_group: null,
                id_truck: null, 
            }
        }
    }, 
    methods: { 
        async loadData() {
            const {data} = await downtimesByReasonPlace({id_place: this.id_place, id_reason: this.id_reason, date: this.date});
            this.shifts = data.shifts;
            
            this.place = { 
                place: data.downtimes[0].title_place,
                type: data.downtimes[0].type, 
                reason: data.downtimes[0].reason
            };
            const groupsTrucks = data.downtimes.map(r=>({id_technica_group: r.id_technica_group, title_technica_type: r.title_technica_type}));
            this.truckGroupTypes = groupsTrucks.reduce((acc, curr, ind) => {
                if(!acc.find(a => a.id_technica_group === curr.id_technica_group))
                {
                    acc.push(curr);   
                }
                return acc;
            }, []);
            
            const trucks = data.downtimes.map(r => ({id_truck: r.id_truck, title_truck: r.title_truck, title_technica_type: r.title_technica_type, id_technica_group: r.id_technica_group, garage: r.garage}));
            this.trucksList = trucks.reduce((acc, curr, ind) => {
                if(!acc.find(a => a.id_truck === curr.id_truck)){
                    acc.push(curr);
                }
                return acc;
            }, []);
            this.downtimes = data.downtimes;

            this.filterDowntimes();

        },
        filterDowntimes(reset) {
            let downtimes = this.downtimes;
            let trucks = this.trucksList;

            if(this.downtimesFilter.id_technica_group)
            {
                downtimes = downtimes.filter(d => d.id_technica_group == this.downtimesFilter.id_technica_group);
                trucks = trucks.filter(e => e.id_technica_group == this.downtimesFilter.id_technica_group);
            }

            if(reset == 'reset')
            {
                this.downtimesFilter.id_truck = null;
            }

            if(this.downtimesFilter.id_truck) 
            {
                downtimes = downtimes.filter(d => d.id_truck == this.downtimesFilter.id_truck);
            }
            
            this.trucksListFiltered = trucks;
            this.downtimesFiltered = downtimes;
        },

        getSummaries(param){ 
            const {columns, data} = param; 

            const sums = [];
            columns.forEach((column, index) => {
                if((0 <= index && index <= 4) || (6 <= index && index <= 7))
                {
                    sums[index] = ``;
                    return ;
                }
                if(index  == 5)
                {
                    sums[index] = 'Сумма простоев';
                    return;
                }
                if(index == 8) 
                {
                    sums[index] = Math.round(this.downtimesFiltered.reduce((acc, curr,index) => acc+= +curr.sinceDuration, 0)*100)/100;
                }
            });

            return sums;
        }, 
        truckFilter(search)
        {
            this.trucksListFiltered =  this.trucksList.filter(r => (r.garage.indexOf(search) != -1 || r.title_truck.indexOf(search) != -1 ));
        }, 

        exportExcel() 
        {
            import("@/vendor/Export2Excel").then((excel) => {
                const headers = this.excelHeader();
                const data = this.excelGetData();

                excel.export_json_to_excel({
                   multiHeader: headers.multiHeader,
                   header: headers.header,
                   data,
                   merges: headers.merges,
                   
                });
                
            });
        },
        excelHeader() 
        {
            const columns = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                             'AA', 'AB', 'AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                             'BA', 'BB', 'BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                            ];

            const multiHeader = [];

            const firstRow = ['', 'Площадка', '', 'Тип причины', '', 'Причина', '', 'На дату', ''];
            const secondRow = ['', 
                                    this.place.place, '', 
                                    this.place.type == 2 ? 'Внеплановый' : 'Плановый', '', 
                                    this.place.reason,  '',
                                    'С: '+new Date(this.shifts.shift_month_start).toLocaleString(undefined, {year:'numeric', month:'numeric', day:'numeric', hour:'2-digit', minute:'2-digit'}).replace(',', ' '), ''
                                ];
            const thirdRow = ['', '', '', '', '', '', '', 'По: '+ new Date(this.shifts.shift_end).toLocaleString(undefined, {year:'numeric', month:'numeric', day:'numeric', hour:'2-digit', minute:'2-digit'}).replace(',', ' '), '' ]; 
            const fourthRow = ['','','','','','','','', '']; 
            const fivthRow = ['№ п/п', 'Тип автосамосвала', 'Автосамосвал', 'Гараж №', 'Код', 'Описание кода простоя', 'Дата начала простоя', 'Дата окончания простоя', 'Длительность (в час)'];
            
            const merges = ['B1:C1',  // Первая строка Площадка на 2 колонки
                            'D1:E1',  // Первая строка Автосамосвал на 2 колонки
                            'F1:G1',  // Первая строка Причина на 2 колонки
                            'H1:I1', // Первая строка На дату на 2 колонки

                            'B2:C3',  // Вторая строка Площадка на 2 строки и на 2 колонки
                            'D2:E3',  // Вторая строка Тип причины
                            'F2:G3',  // Вторая строка Причины

                            'H2:I2', // на дату с
                            'H3:I3'  // на дату по
            ];
            multiHeader.push(firstRow, secondRow, thirdRow, fourthRow); 
            
            return { 
                multiHeader, 
                header: fivthRow, 
                merges
            };
        },
        excelGetData() {
            let summary = 0;
            const data = this.downtimesFiltered.map((d, i) => {
                summary += +d.sinceDuration;
                return [
                    i+1, 
                    d.title_technica_type,
                    d.title_truck, 
                    d.garage,
                    d.code, 
                    d.title_code, 
                    new Date(d.start_time).toLocaleString(undefined, {year: 'numeric', month:'numeric', day:'numeric', hour:'2-digit', minute:'2-digit'}).replace(',', ' '),
                    new Date(d.end_time).toLocaleString(undefined, {year: 'numeric', month:'numeric', day:'numeric', hour:'2-digit', minute:'2-digit'}).replace(',', ' '),
                    d.sinceDuration
                ]}); 
            console.log(summary);
            data.push(['','','','','','Сумма простоев', '','', Math.round(summary*100)/100]);
            return data;
        },


    }   
}

</script>


<style scoped>

.autocomplete-list-item {
    border: 1px solid black;
    padding: 5px; 
    font-size: 15px;
}
.autocomplete-list-item:hover { 
    background-color: #e2fcff;
}
.el-autocomplete-suggestion__wrap
{
    max-height: 400px;
}
.el-select-dropdown__item.truckOption { 
    min-height: 100px;
    height: auto;
    max-width: 600px;
    margin-bottom: 5px;
}
.el-select-dropdown__item.truckOption.all { 
    min-height: 50px !important;
    height: auto;
    max-width: 600px;
    margin-bottom: 5px;
} 
.el-select-dropdown__item.reasonsOption.all {
    min-height: 50px !important;
}
</style>