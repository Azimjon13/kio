<template>
    <el-dialog title="Добавление простоя автосамосвала" width="50%" :visible.sync="this.show" :before-close="close">
        <div class="form-container">
            <el-form ref="addDowntimeTruckForm" :model="downtime" label-position="top" >
                <el-descriptions :column="1" border :label-style="{'min-width': '270px'}">
                    <el-descriptions-item >
                        <template slot="label">
                           Выберите тип автосамосвала
                        </template>
                        <el-select v-model="downtime.technica_group"   :disabled="true">
                            <el-option 
                                v-for="g in truckGroups" 
                                :value="g.id" 
                                :label="g.name"
                            ></el-option>
                        </el-select>
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            Автосамосвал
                        </template>
                        <el-select
                            :disabled="true"
                            v-model="downtime.truck"
                            placeholder="Введите для поиска автосамосвала"
                            filterable
                            remote
                            reserve-keyword
                            :remote-method="searchTruckSelect"
                            :loading="loadingTruck"
                            value-key="id"
                        >
                            <el-option class="truckOption" 
                                    v-for="truck in trucksList" 
                                    :key="truck.id" 
                                    :label="truck.title" 
                                    :value="truck"
                            >
                                <div class="autocomplete-list-item">
                                    <span class="name-autocomplete">
                                        <b>Наименование:</b> {{ truck.title }}
                                    </span>
                                    <br>
                                    <span class="garage-autocomplete">
                                        <b>Гаражный №:</b>  {{ truck.garage }}
                                    </span>
                                </div>
                            </el-option>
                        </el-select>
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                           Код простоя
                        </template>
                        <el-select
                            v-model="downtime.code"
                            filterable
                            remote 
                            reserve-keyword
                            :remote-method="searchCodeSelect"
                            :loading="loadingCode"
                            value-key="id"
                            :disabled="true"
                        >
                            <el-option 
                                class="codeOption" 
                                v-for="code in codes" 
                                :key="code.id" 
                                :label="code.code" 
                                :value="code"
                            >
                                <div class="autocomplete-list-item">
                                    <span class="code-autocomplete">
                                        <b>Код:</b> {{ code.code }}
                                    </span>
                                    <span class="reason-type-autocomplete">
                                        <b>Тип:</b> {{ code.reason_type == 1 ? "Плановый" :  "Внеплановый"}}
                                    </span>
                                    <span class="reason-autocomplete">
                                        <b>Причина:</b>  {{ code.reason }}
                                    </span>
                                    <div class="code-description-autocomplete">
                                        <b>Описание кода простоя: </b> {{ code.title }}
                                    </div>
                                </div>
                            </el-option>
                        </el-select>
                        <el-tooltip 
                                v-if="downtime.code" 
                                effect="dark" 
                                content="Показать/Скрыть описание кода простоя" 
                                placement="right"
                                style="float: right;"
                        >
                            <el-button type="info" icon="el-icon-view" circle @click="viewCodeDescribeToggle"></el-button>
                        </el-tooltip>
                    </el-descriptions-item> 
                    <el-descriptions-item v-if="downtime.code && viewCodeDescribe">
                        <template slot="label">
                           Тип простоя 
                        </template>
                        {{ downtime.code.reason_type == 1 ? "Плановый" : "Внеплановый" }}
                    </el-descriptions-item> 
                    <el-descriptions-item v-if="downtime.code && viewCodeDescribe">
                        <template slot="label">
                           Вид простоя
                        </template>
                        {{ downtime.code.reason }}
                    </el-descriptions-item> 
                    <el-descriptions-item v-if="downtime.code && viewCodeDescribe">
                        <template slot="label">
                           Описание к коду простоя
                        </template>
                        {{ downtime.code.title }}
                    </el-descriptions-item> 
                    <el-descriptions-item>
                        <template slot="label">
                           Дата и время начала простоя
                        </template>
                        <el-date-picker 
                                :disabled="true"
                                ref="pickerStart"
                                v-if="downtime.truck"
                                type="datetime" 
                                @focus="focusStart"
                                v-model="downtime.start_time"
                                value-format="timestamp"
                                format="dd.MM.yyyy HH:mm:ss"
                                :picker-options = "startPickerOptions"
                        ></el-date-picker>
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                           Дата и время окончания простоя
                        </template>
                        <el-date-picker 
                                ref="pickerEnd"
                                v-if="downtime.start_time"
                                :disabled = "!downtime.start_time"
                                type="datetime" 
                                @focus="focusEnd"
                                v-model="downtime.end_time"
                                value-format="timestamp"
                                format="dd.MM.yyyy HH:mm:ss"
                                :picker-options = "endPickerOptions"
                        ></el-date-picker>
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            Коментарий
                        </template>
                        <el-input type="textarea" v-model="downtime.details"></el-input>
                    </el-descriptions-item>
                </el-descriptions>
            </el-form>
            <br>
            <span slot="footer" class="dialog-footer" style="float:right;">
                <el-button type="primary" @click="saveDowntime">
                    Сохранить
                </el-button>
                <el-button type="danger" @click="close">
                    {{ $t('table.cancel') }}
                </el-button>
            </span>
            <br><br>
        </div>
    </el-dialog>
</template>


<script>
import {getTruckGroups, searchTruck, updateDowntime, getDowntime} from "@/api/trucks";
import {searchCode} from "@/api/codes";

export default { 
    props: {
        show: Boolean,
        close: Function, 
        date: String,
        downtimeId: Number,
    },
    async created() {
        await this.loadGroups();
        await this.loadDowntime();
    },
    
    data() {
        return {
            viewCodeDescribe: true, 

            truckGroups: [],

            loadingTruck: false,
            trucksList: [],

            loadingCode: false, 
            codes: [],


            downtime: {
                technica_group: null,
                truck: null,
                code: null,
                id_place: null, 
                start_time: null, 
                end_time: null,
                details: "",
            }, 
            startPickerOptions: { 
                disabledDate: this.disabledStartDate,
            }, 
            endPickerOptions: {
                disabledDate: this.disabledEndDate,
            },
        }
    },
    methods: {
        async loadDowntime() 
        {
            const {data} = await getDowntime(this.downtimeId);
            this.trucksList = [data.place_truck]; 
            this.codes = [data.code]; 
            
            this.downtime = {
                ...data, 
                technica_group: data.code.id_technica_group,
                start_time: new Date(data.start_time).getTime(),
                end_time: data.end_time ? new Date(data.end_time).getTime() : null,
                truck: data.place_truck,
            };
        },
        viewCodeDescribeToggle() 
        {
            this.viewCodeDescribe = !this.viewCodeDescribe;
        },
        focusStart: function() {
            this.$nextTick(_ => {
                this.$refs.pickerStart.picker.date = new Date()
            });
        },
        disabledStartDate(time) 
        {
            let dateFrom = new Date(this.downtime.truck.date_from).getTime() - 86400*1000;
            let dateTo = new Date(this.downtime.truck.date_to).getTime();
            
            let currentPicking = time.getTime();
                   
            if(dateTo === 0 && dateFrom < currentPicking) 
                return false;
            else if(dateFrom < currentPicking && currentPicking < dateTo) 
                return false; 
            return true; 
        },
        disabledEndDate(time) 
        {
            let dateFrom = new Date(this.downtime.truck.date_from).getTime() - 86400*1000;
            let dateTo = new Date(this.downtime.truck.date_to).getTime();
            
            let startTime = new Date(this.downtime.start_time).getTime() - 86400*1000;

            let currentPicking = time.getTime();
                   
            if(dateTo === 0 && dateFrom < currentPicking && startTime < currentPicking) 
                return false;
            else if(dateFrom < currentPicking && currentPicking < dateTo && startTime < currentPicking) 
                return false; 
            return true; 
        },
        focusEnd: function() {
            this.$nextTick(_ => {
                this.$refs.pickerEnd.picker.date = new Date()
            });
        },

        async loadGroups() {
            const {data} = await getTruckGroups();
            this.truckGroups = data;
        },

        async searchCodeSelect(search) 
        {
            if(search.trim() !== '' && search.length >= 2) 
            {
                this.loadingCode = true; 
                const query = {
                    id_technica_group: this.downtime.technica_group, 
                    search: search, 
                }; 
                const res = await searchCode(query);
                this.loadingCode = false;
                if(res.data != null)
                {
                    this.codes = res.data; 
                }
            }
            else {
                this.codes = [];
            }
        }, 
        async searchTruckSelect(search)
        {
            if(search.trim() !== '' && search.length >= 2) 
            {
                this.loadingTruck = true;
                const query = {
                    id_group: this.downtime.technica_group, 
                    id_place: this.downtime.id_place, 
                    date: this.date,
                    search: search,
                };
                let {data} = await searchTruck(query);
                this.loadingTruck = false;
                if(data != null)
                {
                    this.trucksList = data;
                }
            }
            else
            {
                this.trucksList = [];
            }
        },
   
        async saveDowntime()
        {
            const newDowntime = {
                id_place: this.downtime.id_place, 
                id_truck: this.downtime.id_truck, 
                id_place_truck: this.downtime.id_place_truck, 
                id_code: this.downtime.id_code, 
                start_time: this.downtime.start_time, 
                end_time: this.downtime.end_time, 
                details: this.downtime.details
            };

            const res = await updateDowntime(this.downtime.id, newDowntime);
            if(!res.errors && res.status) 
            {
                this.$message({
                    message: 'Простой автосамосвала успешно добавлен!',
                    type: 'success',
                    duration: 5 * 1000,
                });
                this.close();
                return; 
            }
            this.$message({
                message:res.message && res.errors,
                type: 'error',
                duration: 5 * 1000,
            });
            console.log(res);
        }, 
    }
}

</script>


<style>
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
.reason-type-autocomplete { 
    float: right;
}
.code-description-autocomplete
{
    width: 100%; 
    white-space: break-spaces;
}
.el-select-dropdown__item.codeOption {
    min-height: 120px; 
    max-width: 750px;
    height: auto;
    margin-bottom: 5px;
}
.el-select-dropdown__item.truckOption { 
    min-height: 100px;
    height: auto;
    max-width: 600px;
    margin-bottom: 5px;
}

.el-select-dropdown__wrap { 
    max-height: 360px;
}
</style>