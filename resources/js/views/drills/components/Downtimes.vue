<template>
    <div class="app-container">
        <h3>Остановки буровых станков за сегодня {{ showTable ? "": "(скрыто)" }}

            <el-button 
                    type="success" 
                    icon="el-icon-plus" 
                    style="float: right;"
                    @click="openAdd"
                    v-if="checkPermission(['manage drills'])"
            >Добавить простой</el-button>
            <el-button
                type="primary"
                plain
                v-if="!showTable"
                @click="showTable = true;"
                style="float: right;"
            >
                Показать простои за сегодня
            </el-button>
            <el-button
                type="primary"
                plain
                v-else
                @click="showTable = false;"
                style="float: right;"
            >
                Скрыть простои за сегодня
            </el-button>
        </h3>
            <div>Начало сутки: {{ shifts.shift_start }}</div>
            <div>Конец сутки: &nbsp; {{ shifts.shift_end }}</div>
        <br>
        <br/>
        <div v-if="this.showTable">
            <el-form :model="downtimesFilter" label-position="top">
                <el-row :gutter="20">
                    <el-col :span="4">
                        <el-form-item label="Введите для поиска:">
                            <el-input v-model="downtimesFilter.search"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="Фильтр по буровым станкам" label-position="top">
                            <el-select 
                                v-model="downtimesFilter.selectedDrill" 
                                filterable
                                value-key="id_drill"
                                :filter-method="drillFilter"
                            >
                                <el-option class="drillOption all"
                                    :key="null"
                                    label="Все"
                                    :value="null"
                                >
                                    <div class="autocomplete-list-item all">
                                        <span class="name-autocomplete">
                                            Все буровые станки
                                        </span>
                                    </div>
                                </el-option>
                                <el-option class="drillOption"
                                    v-for="drill in drillsListFiltered"
                                    :key="drill.id_drill"
                                    :label="drill.title"
                                    :value="drill"
                                >
                                    <div class="autocomplete-list-item">
                                            <span class="name-autocomplete">
                                                <b>Наименование:</b> {{ drill.title }}
                                            </span>
                                            <br>
                                            <span class="garage-autocomplete">
                                                <b>Гаражный №:</b>  {{ drill.garage }}
                                            </span>
                                        </div>
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="Фильтр по причинам остановки">
                            <el-select 
                                v-model="downtimesFilter.selectedReason" 
                                filterable
                                value-key="id_reason"
                                :filter-method="reasonFilter"
                            >
                                <el-option class="reasonsOption all"
                                    :key="null"
                                    label="Все"
                                    :value="null"
                                >
                                    <div class="autocomplete-list-item">
                                        <span class="name-autocomplete">
                                            Все причины
                                        </span>
                                    </div>
                                </el-option>
                                <el-option class="drillOption"
                                    v-for="reason in reasonsListFiltered"
                                    :key="reason.id_reason"
                                    :label="reason.reason"
                                    :value="reason"
                                >
                                    <div class="autocomplete-list-item">
                                            <span class="name-autocomplete">
                                                <b>Наименование:</b> {{ reason.reason }}
                                            </span>
                                            <br>
                                            <span class="garage-autocomplete">
                                                <b>Тип:</b>  {{ reason.type == 2 ? "Внеплановый": "Плановый" }}
                                            </span>
                                        </div>
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-button type="success" style="margin-top: 45px;" @click="paginate">Фильтр/Поиск</el-button>
                    </el-col>
                </el-row>
            </el-form>

        </div>

        <el-table
            :data="showDowntimesList"
            ref="downtimesTable"
            fit  highlight-current-row border style="width: 100%;" align="center"
            v-show="this.showTable"
        >
            <el-table-column type="expand">
                <template slot-scope="props">
                    <div class="code-description" style="padding: 5px 15px;">
                        <el-row :gutter="20">
                            <el-col :span="8">
                                <span><b>Код: &nbsp; </b> {{ props.row.code }}</span>
                            </el-col>
                            <el-col :span="8">
                                <span><b>Вид причины: &nbsp; </b> {{ props.row.reason }}</span>
                            </el-col>
                            <el-col :span="8">
                                <span><b>Тип простоя: &nbsp; </b>{{ props.row.type == 1 ? "Плановый" :  "Внеплановый" }}</span>
                            </el-col>
                            <el-col :span="24">
                                <hr>
                                <span><b>Описание кода: &nbsp; </b> {{ props.row.title }}</span>
                            </el-col>
                        </el-row>
                    </div>
                </template>
            </el-table-column>
            <el-table-column :min-width="4"  label="№ п/п" align="center" width="50">
                <template slot-scope="scope">
                    {{ scope.$index+1 }}
                </template>
            </el-table-column>
            <el-table-column :min-width="5"  label="Код" prop="code" align="center">
            </el-table-column>
            <el-table-column :min-width="14" label="Буровой станок" prop="title_drill" align="center"></el-table-column>
            <el-table-column :min-width="7"  label="Гараж №" prop="garage" align="center"></el-table-column>
            <el-table-column :min-width="18" label="Причина" prop="reason" align="center"></el-table-column>
            <el-table-column :min-width="12" label="Дата начала" prop="start_time" align="center">
                <template slot-scope="scope">
                    {{ new Date(scope.row.start_time).toLocaleString(undefined, {year:"numeric", month:"numeric", day:"numeric", hour:"2-digit", minute:"2-digit"}).replace(',', ' ') }}
                </template>
            </el-table-column>
            <el-table-column :min-width="12" label="Дата окончания" prop="end_time" align="center">
                <template slot-scope="scope">
                    <span v-if="scope.row.end_time">
                        {{ new Date(scope.row.end_time).toLocaleString(undefined, {year:"numeric", month:"numeric", day:"numeric", hour: "2-digit", minute:"2-digit"}).replace(',',' ') }}
                    </span>
                    <span v-else> 
                        простой не окончен
                    </span>
                </template>
            </el-table-column>
            <el-table-column :min-width="20" label="Продолжительность (часы)" align="center">
                <el-table-column :min-width="10"  label="За сегодня" prop="dayDuration" align="center"></el-table-column>
                <el-table-column :min-width="10"  label="С начала остановки" prop="sinceDuration" align="center"></el-table-column>
            </el-table-column>
            <el-table-column :min-width="8"  label="Действия" align="center">
                <template slot-scope="scope">
                    <el-button v-if="!scope.row.end_time && checkPermission(['manage drills'])"  type="info" icon="el-icon-edit" circle @click="openEdit(scope.row.id_downtime)"></el-button>
                    <el-button v-if="checkPermission(['manage drills'])" type="danger" icon="el-icon-delete" circle @click="remove(scope.row.id_downtime)"></el-button>
                </template>
            </el-table-column>
        </el-table>
        <Pagination :page-sizes="[5, 7, 10, 15, 20]" v-show="downtimesFilter.total>5 && showTable" :total="downtimesFilter.total" :page.sync="downtimesFilter.page" :limit.sync="downtimesFilter.per_page" @pagination="paginate" :autoScroll="false"></Pagination>
        <AddDowntime  v-if="checkPermission(['manage drills'])" :show="showAdd" :close="closeAdd" :placeId="id_place" :date="date"></AddDowntime>
        <EditDowntime  v-if="editingDowntimeId && checkPermission(['manage drills'])" :show="showEdit" :close="closeEdit" :placeId="id_place" :date="date" :downtimeId="editingDowntimeId"></EditDowntime>
    </div>
</template>


<script>
import {getDrillDowntimesDailiy, deleteDowntime} from '@/api/drills';
import AddDowntime from './AddDowntime.vue';
import EditDowntime from './EditDowntime.vue';
import checkPermission from '@/utils/permission'; // Permission checking
import Pagination from '@/components/Pagination';
export default {
    props: {
        id_place: Number,
        date: String,
        updater: Function
    },
    watch: {
        date: function (newVal, oldVal) {
            this.loadDowntimes();
        }, 
        id_place: function(newVal, oldVal) 
        {
            this.loadDowntimes();
        }
    },
    mounted() {
        this.loadDowntimes();
    },
    data() {
        return {
            showTable: false,
            showAdd:false, 
            showEdit: false,
            editingDowntimeId: null, 
            downtimesList: [], 
            showDowntimesList: [],
            
            drillsList: [], 
            drillsListFiltered: [],

            reasonsList: [], 
            reasonsListFiltered: [],
            


            shifts: {},
            downtimesFilter: {
                search:"", 
                selectedDrill: null,
                selectedReason: null,
                selectedType: null,
                page: 1, 
                total: 0, 
                per_page: 5,
            },
        };
    },
    methods: {
        checkPermission,
        async loadDowntimes() {
            const { data } = await getDrillDowntimesDailiy(this.date, this.id_place);
            this.downtimesList = data.downtimes;
            this.shifts = {
                shift_start: new Date(data.shifts.shift_start).toLocaleString(undefined, {year:"numeric", month:"numeric", day:"numeric", hour: "2-digit", minute:"2-digit"}).replace(',', ''), 
                shift_end:  new Date(data.shifts.shift_end).toLocaleString(undefined, {year:"numeric", month:"numeric", day:"numeric", hour: "2-digit", minute:"2-digit"}).replace(',', ''),
            };

            const reasons = data.downtimes.map(d => ({
                id_reason: d.id_reason,
                reason: d.reason,
                type: d.type,
            })); 

            this.reasonsList = reasons.filter((r,i,self) => i===self.findIndex((t) =>  t.id_reason === r.id_reason));
            this.reasonsListFiltered = this.reasonsList;
       
            const drills = data.downtimes.map(d => ({
                id_drill: d.id_drill, 
                title: d.title_drill,
                garage: d.garage,
            }));

            this.drillsList = drills.filter((e,i,self) => i === self.findIndex((t) => t.id_drill === e.id_drill));
            this.drillsListFiltered = this.drillsList;

            
            this.downtimesFilter = {
                page: 1, 
                search:"", 
                selectedDrill: null,
                selectedReason: null,
                selectedType: null,
                total: this.downtimesList.length, 
                per_page: 5,
            };
            this.paginate();
        }, 
        paginate(p) 
        {
            if(this.downtimesFilter.selectedDrill || this.downtimesFilter.selectedReason || this.downtimesFilter.search.trim()){
                const s = this.downtimesFilter.search.trim();
                let filtered = this.downtimesList;

                if(this.downtimesFilter.selectedDrill)
                {
                    filtered = filtered.filter(r => r.id_drill == this.downtimesFilter.selectedDrill.id_drill);
                }
                if(this.downtimesFilter.selectedReason)
                {
                    filtered = filtered.filter(r => r.id_reason == this.downtimesFilter.selectedReason.id_reason);
                }
                if(s)
                {
                    filtered = filtered.filter(r => (r.code.indexOf(s) != -1 || r.garage.indexOf(s) != -1 || r.title_drill.indexOf(s) != -1 || r.reason.indexOf(s) != -1));
                }
                this.downtimesFilter.total = filtered.length;
                if(p.page)
                    this.downtimesFilter.page = p.page;
                else 
                    this.downtimesFilter.page = 1;
                // this.downtimesFilter.page = 1; 

                const from = ( this.downtimesFilter.page - 1 ) * this.downtimesFilter.per_page;
                const to = (this.downtimesFilter.page * this.downtimesFilter.per_page ) - 1;
                this.showDowntimesList = filtered.filter((d,i) => from <= i && i <= to);
            }
            else 
            { 
                this.downtimesFilter.total = this.downtimesList.length;
               
                const from = ( this.downtimesFilter.page - 1 ) * this.downtimesFilter.per_page;
                const to = (this.downtimesFilter.page * this.downtimesFilter.per_page ) - 1;
                this.showDowntimesList = this.downtimesList.filter((d,i) => from <= i && i <= to);
            }
        },
        openEdit(id) 
        {
            this.editingDowntimeId = id; 
            this.showEdit = true; 
        }, 
        closeEdit()
        {
            this.editingDowntimeId = null;
            this.showEdit = false;
            this.loadDowntimes();
            this.updater();
        },
        openAdd()
        {
            this.showAdd = true;
        },
        closeAdd() {
            this.showAdd = false;
            this.loadDowntimes();
            this.updater();
        },
        async remove(downtimeId)
        {
            try { 
                const conf = await this.$confirm(`Вы точно хотите удалить данные простоя?`, `Внимание!`, {type:'warning'});
                if(conf != "confirm")
                    return;
            }
            catch(e) 
            {
                console.log(e); return;
            }
            const res = await deleteDowntime(downtimeId);
            if(!res.errors && res.status)
            {
                this.$message({
                    message: `Удаление простоя прошла успешно!`,
                    type:"success",
                    duration: 5*1000,
                });
                this.loadDowntimes();
                this.updater();
                return;
            }
            this.$message({
                message:res.errors,
                type:"error",
                duration:5*1000
            });
        }, 

        drillFilter(search)
        {
            this.drillsListFiltered =  this.drillsList.filter(r => (r.garage.indexOf(search) != -1 || r.title.indexOf(search) != -1 ));
        }, 
        reasonFilter(search)
        {
            this.reasonsListFiltered = this.reasonsList.filter(r => (r.reason.indexOf(search) != -1));
        }
    },
    components: { AddDowntime, EditDowntime, Pagination }
};


</script>
<style scoped>
.el-table__cell .cell {
  word-break: break-word !important;
}
.el-select-dropdown__item.drillOption.all { 
    min-height: 50px !important;
    height: auto;
    max-width: 600px;
    margin-bottom: 5px;
} 
.el-select-dropdown__item.reasonsOption.all {
    min-height: 50px !important;
}
</style>