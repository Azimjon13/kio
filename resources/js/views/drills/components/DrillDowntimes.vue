<template>
    <el-dialog 
        title="Данные простоев по буровому станку с начала месяца" 
        width="70%" 
        :visible.sync="this.show" 
        :before-close="close"
    >
        <div class="form-container">
            <el-descriptions direction="vertical" :column="4" border>
                <el-descriptions-item label="Площадка">
                    {{place.place}}
                </el-descriptions-item>
                <el-descriptions-item label="Буровой станок">
                    {{ place.drill }}
                </el-descriptions-item>
                <el-descriptions-item label="Гараж №">
                    {{ place.garage }}
                </el-descriptions-item>
                <el-descriptions-item label="На дату">
                    <span>С {{ new Date(shifts.shift_month_start).toLocaleString(undefined, {year:'numeric', month:'numeric', day:"numeric", hour:'2-digit', minute:'2-digit'}).replace(',', ' ') }}</span> 
                    <br>
                    <span>По {{ new Date(shifts.shift_end).toLocaleString(undefined, {year:'numeric', month:'numeric', day:"numeric", hour:'2-digit', minute:'2-digit'}).replace(',', ' ') }}</span>
                </el-descriptions-item>
            </el-descriptions>

            <br>
            <el-select v-model="downtimesFilter.type" @change="filterDowntimes('reset')">
                <el-option :value="null" label="Все"></el-option>
                <el-option :value="1" label="Плановые"></el-option>
                <el-option :value="2" label="Внеплановые"></el-option>
            </el-select>

            <el-select v-model="downtimesFilter.id_reason" @change="filterDowntimes">
                <el-option :value="null" label="Все"></el-option>
                <el-option v-for="reason in filteredReasons" :value="reason.id_reason" :label="reason.reason"></el-option>
            </el-select>

            <el-button type="success" @click="exportExcel" style="float:right;">Excel</el-button>
            <br>
            <el-table
                :data="downtimesFiltered"
                fit  highlight-current-row border style="width: 100%;" align="center"
                ref="drillDowntimes"
                class="drillDowntimesTable"
                :summary-method="getSummaries"
                show-summary
            >
                <el-table-column label="№ п/п" :min-width="4" align="center">
                    <template slot-scope="scope">{{ scope.$index+1 }}</template>
                </el-table-column>
                <el-table-column label="Тип простоя" :min-width="9" align="center">
                    <template slot-scope="scope">
                        {{ scope.row.type == 1 ? "Плановый" : "Внеплановый" }}
                    </template>
                </el-table-column>
                <el-table-column label="Вид простоя" prop="reason" :min-width="15" align="center"></el-table-column>
                <el-table-column label="Код" prop="code" :min-width="5" align="center"></el-table-column>
                <el-table-column label="Описание кода простоя" prop="title_code" :min-width="34" align="center"></el-table-column>
                <el-table-column label="Дата начала простоя" prop="start_time" :min-width="11" align="center">
                    <template slot-scope="scope">
                        {{ new Date(scope.row.start_time).toLocaleString(undefined, {year:"numeric", month:"numeric", day:"numeric", hour:"2-digit", minute:"2-digit"}).replace(',', ' ') }}
                    </template>
                </el-table-column>
                <el-table-column label="Дата окончания простоя" prop="end_time" :min-width="11" align="center">
                    <template slot-scope="scope">
                        {{ scope.row.end_time ? new Date(scope.row.end_time).toLocaleString(undefined, {year:"numeric", month:"numeric", day:"numeric", hour:"2-digit", minute:"2-digit"}).replace(',', ' ') : " не окончен "}}
                    </template>
                </el-table-column>
                <el-table-column label="Длительность (в час)" prop="sinceDowntime" :min-width="11" align="center"></el-table-column>
            </el-table>
        </div>
    </el-dialog>    
</template>

<script>
import { drillDowntimes } from '@/api/drills';

export default  {
    props: {
        show: Boolean,
        close: Function, 
        id_place_drill: Number,
        date: String,
    },
    data() {
        return {
            downtimes: [],
            downtimesFiltered: [],

            reasonsList: [],
            filteredReasons: [],

            downtimesFilter: { 
                id_reason: null, 
                type: null, 
            },
            place:{},
            shifts: {},
        }
    },
    created() {
        this.loadDowntimesDrill();
    },
    methods:{
        async loadDowntimesDrill() {
            const {data} = await drillDowntimes({id_place_drill: this.id_place_drill, date: this.date});
            this.downtimes = data.downtimes;
            const reasons = data.downtimes.map(d => ({
                id_reason: d.id_reason, 
                reason: d.reason, 
                type: d.type, 
            }));

            this.reasonsList = reasons.reduce((acc, curr, ind) => {
                if(!acc.find(v => v.id_reason == curr.id_reason))
                {
                    acc.push(curr);
                }
                return acc;
            }, []);
           

            this.place = data.place;
            this.shifts = data.shifts;

            this.filterDowntimes();
        },
        filterDowntimes(reset) 
        {
            let downtimes = this.downtimes;
            let reasons = this.reasonsList;

            if(this.downtimesFilter.type) 
            {
                reasons = reasons.filter(r => r.type == this.downtimesFilter.type);
                downtimes = downtimes.filter(d => d.type == this.downtimesFilter.type);
            }

            if(reset == 'reset')
            {
                this.downtimesFilter.id_reason = null;
            }

            if(this.downtimesFilter.id_reason)
            {
                downtimes = downtimes.filter(d => d.id_reason == this.downtimesFilter.id_reason);
            }

            this.downtimesFiltered = downtimes;
            this.filteredReasons = reasons;
             
        },
        getSummaries(param){ 
            const {columns, data} = param; 

            const sums = [];
            columns.forEach((column, index) => {
                if((0 <= index && index <= 3) || (5 <= index && index <= 6))
                {
                    sums[index] = ``;
                    return ;
                }
                if(index  == 4)
                {
                    sums[index] = 'Сумма простоев';
                    return;
                }
                if(index == 7) 
                {
                    sums[index] = Math.round(this.downtimesFiltered.reduce((acc, curr,index) => acc+= +curr.sinceDowntime, 0)*100)/100;
                }
            });

            return sums;
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

            const firstRow = ['', 'Площадка', '', 'Буровой станок', '', 'Гараж №', 'На дату', ''];
            const secondRow = ['', this.place.place, '', this.place.drill, '', this.place.garage, 'С: '+new Date(this.shifts.shift_month_start).toLocaleString(undefined, {year:'numeric', month:'numeric', day:'numeric', hour:'2-digit', minute:'2-digit'}).replace(',', ' '), ''];
            const thirdRow = ['', '', '', '', '', '', 'По: '+ new Date(this.shifts.shift_end).toLocaleString(undefined, {year:'numeric', month:'numeric', day:'numeric', hour:'2-digit', minute:'2-digit'}).replace(',', ' '), '' ]; 
            const fourthRow = ['','','','','','','','']; 
            const fivthRow = ['№ п/п', 'Тип простоя', 'Вид простоя', 'Код', 'Описание кода простоя', 'Дата начала простоя', 'Дата окончания простоя', 'Длительность (в час)'];
            
            const merges = ['B1:C1',  // Первая строка Площадка на 2 колонки
                            'D1:E1',  // Первая строка Буровой станок на 2 колонки
                            'G1:H1',  // Первая строка На дату на 2 колонки
                            'B2:C3',  // Вторая строка Площадка на 2 строки и на 2 колонки
                            'D2:E3',  // Вторая строка Буровой станок на 2 строки и на 2 колонки
                            'F2:F3',  // Вторая строка гаражный номер
                            'G2:H2',  // Вторая строка Начала месяца
                            'G3:H3',  // Третья строка Тек день
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
                summary += +d.sinceDowntime;
                return [
                    i+1, 
                    d.type == 1 ? 'Плановый' : 'Внеплановый', 
                    d.reason, 
                    d.code, 
                    d.title_code, 
                    new Date(d.start_time).toLocaleString(undefined, {year: 'numeric', month:'numeric', day:'numeric', hour:'2-digit', minute:'2-digit'}).replace(',', ' '),
                    new Date(d.end_time).toLocaleString(undefined, {year: 'numeric', month:'numeric', day:'numeric', hour:'2-digit', minute:'2-digit'}).replace(',', ' '),
                    d.sinceDowntime
                ]}); 
            data.push(['', '', '', '', 'Сумма простоев', '', '', summary]);
            return data;
        },
    }
}
</script>
<style scoped>
.el-table__cell .cell {
  word-break: break-word !important;
}
.el-table__footer-wrapper>.el-table__footer>tbody>tr>td>.cell {
    font-weight: bold !important;
}
</style>