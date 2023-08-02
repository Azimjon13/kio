<template>
    <el-dialog title="Добавление простоя экскаватора" width="50%" :visible.sync="this.show" :before-close="close">
        <div class="form-container">
            <el-form ref="addDowntimeExcavatorForm" :model="downtime" label-position="top" >
                <el-descriptions :column="1" border :label-style="{'min-width': '270px'}">
                    <el-descriptions-item >
                        <template slot="label">
                           Выберите тип экскаватора
                        </template>
                        <el-select v-model="downtime.technica_group">
                            <el-option 
                                v-for="g in excavatorGroups" 
                                :value="g.id" 
                                :label="g.name"
                            ></el-option>
                        </el-select>
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                           Экскаватор
                        </template>
                        <el-select
                            :disabled="!downtime.technica_group"
                            v-model="downtime.excavator"
                            placeholder="Введите для поиска экскаватора"
                            filterable
                            remote
                            reserve-keyword
                            :remote-method="searchExcavatorSelect"
                            :loading="loadingExcavator"
                            value-key="id"
                        >
                            <el-option class="excavatorOption" 
                                    v-for="excavator in excavatorsList" 
                                    :key="excavator.id" 
                                    :label="excavator.title" 
                                    :value="excavator"
                            >
                                <div class="autocomplete-list-item">
                                    <span class="name-autocomplete">
                                        <b>Наименование:</b> {{ excavator.title }}
                                    </span>
                                    <br>
                                    <span class="garage-autocomplete">
                                        <b>Гаражный №:</b>  {{ excavator.garage }}
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
                            :disabled="!downtime.technica_group"
                            v-model="downtime.code"
                            filterable
                            remote 
                            reserve-keyword
                            :remote-method="searchCodeSelect"
                            :loading="loadingCode"
                            value-key="id"
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
                                        <b>Тип:</b> {{ code.reason_type == 1 ? "Плановый" : (code.reason_type == 2 ? "Внеплановый" : "Эксп. вне объёма" )}}
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
                        {{ downtime.code.reason_type == 1 ? "Плановый" : (
                            downtime.code.reason_type == 2 ? "Внеплановый" : 
                            "Эксп. вне объёма" ) 
                        }}
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
                                ref="pickerStart"
                                v-if="downtime.excavator"
                                :disabled = "!downtime.excavator"
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
                    Добавить
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
import {getExcavatorGroups, searchExcavator, addDowntime} from "@/api/excavators";
import {searchCode} from "@/api/codes";

export default { 
    props: {
        show: Boolean,
        close: Function, 
        placeId: Number,
        date: String,
    },
    async created() {
        await this.loadGroups();
    },
    watch: {
        'downtime.technica_group': function(newVal, oldVal) {
            if(newVal != oldVal) 
            {
                this.downtime.excavator = null; 
                this.downtime.code = null; 
                this.downtime.startTime = null;
                this.downtime.end_time = null;
            }
        },
    },
    data() {
        return {
            viewCodeDescribe: true, 

            excavatorGroups: [],

            loadingExcavator: false,
            excavatorsList: [],

            loadingCode: false, 
            codes: [],


            downtime: {
                technica_group: null,
                excavator: null,
                code: null,
                id_place: this.placeId, 
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
            let dateFrom = new Date(this.downtime.excavator.date_from).getTime() - 86400*1000;
            let dateTo = new Date(this.downtime.excavator.date_to).getTime();
            
            let currentPicking = time.getTime();
                   
            if(dateTo === 0 && dateFrom < currentPicking) 
                return false;
            else if(dateFrom < currentPicking && currentPicking < dateTo) 
                return false; 
            return true; 
        },
        disabledEndDate(time) 
        {
            let dateFrom = new Date(this.downtime.excavator.date_from).getTime() - 86400*1000;
            let dateTo = new Date(this.downtime.excavator.date_to).getTime();
            
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
            const {data} = await getExcavatorGroups();
            this.excavatorGroups = data;
        },

        async searchCodeSelect(search) 
        {
            if(search.trim() !== '' && search.length >= 1) 
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
        async searchExcavatorSelect(search)
        {
            if(search.trim() !== '' && search.length >= 1) 
            {
                this.loadingExcavator = true;
                const query = {
                    id_group: this.downtime.technica_group, 
                    id_place: this.placeId, 
                    date: this.date,
                    search: search,
                };
                let {data} = await searchExcavator(query);
                this.loadingExcavator = false;
                if(data != null)
                {
                    this.excavatorsList = data;
                }
            }
            else
            {
                this.excavatorsList = [];
            }
        },
   
        async saveDowntime()
        {
            console.log(this.downtime);
            const newDowntime = {
                id_place: this.placeId, 
                id_excavator: this.downtime.excavator.id_excavator, 
                id_place_excavator: this.downtime.excavator.id, 
                id_code: this.downtime.code.id, 
                start_time: this.downtime.start_time, 
                end_time: this.downtime.end_time, 
                details: this.downtime.details
            };

            const res = await addDowntime(newDowntime);
            if(!res.errors && res.status) 
            {
                this.$message({
                    message: 'Простой эксковатора успешно добавлен!',
                    type: 'success',
                    duration: 5 * 1000,
                });

                this.downtime = {
                    technica_group: null,
                    excavator: null,
                    code: null,
                    id_place: this.placeId, 
                    start_time: null, 
                    end_time: null,
                    details: "",
                    
                };
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
.el-select-dropdown__item.excavatorOption { 
    min-height: 100px;
    height: auto;
    max-width: 600px;
    margin-bottom: 5px;
}


.el-select-dropdown__wrap { 
    max-height: 360px;
}
</style>