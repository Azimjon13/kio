<template>
    <el-dialog  title="Добавление подразделений" :visible.sync="this.show" width="65%" :before-close="close">
        <div class="form-container">
            <el-form ref="addDivsionForm" :model="newDivision" label-position="top" label-width="300px"  style="">
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="Вышестоящее подзразделение/отдел/площадка" label-width="auto">
                            <el-cascader 
                                v-model="newDivision.cascaderSelection" 
                                :options="divisionsForCascader" 
                                :props="propsCascader" 
                                style="width: 100%"
                                :show-all-levels="false"
                                @change="handleCascaderChange"
                            >
                            </el-cascader>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Главное подразделение(управление)" label-width="auto">
                            <el-select v-model="newDivision.id_main" placeholder="Главное управление" style="width: 100%;">
                                <el-option
                                    v-for="main in mainDivisions"
                                    :key="main.id"
                                    :label="main.short_name_ru"
                                    :value="main.id"
                                >
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Тип подразделения" label-width="auto">
                            <el-select v-model="newDivision.id_type" placeholder="Тип подразделения"     style="width: 100%">
                                <el-option
                                    v-for="dType in divisionTypes"
                                    :key="dType.id"
                                    :label="dType.name"
                                    :value="dType.id"
                                >
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <h3 style="color:#5a04b9;">
                    Название подразделения
                </h3>
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="Короткое название на русском" label-position="left">
                            <el-input v-model="newDivision.short_name_ru"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Короткое название на узбекском" label-position="left">
                            <el-input v-model="newDivision.short_name_uz"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="Полное название на русском" label-position="left">
                            <el-input v-model="newDivision.full_name_ru" type="textarea" :autosize="{ minRows: 4, maxRows: 5}" ></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Полное название на узбекском" label-position="left">
                            <el-input v-model="newDivision.full_name_uz" type="textarea" :autosize="{ minRows: 4, maxRows: 5}" ></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>                    
            
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="addDivision">
                    Сохранить
                </el-button>
                <el-button type="danger" @click="close()">
                    {{ $t('table.cancel') }}
                </el-button>
            </span>
        </div>
    </el-dialog>
</template>

<script>

import {fetchChildDivisions, fetchDivisionTypes, createDivision} from "@/api/divisions";


export default {
    props: {
        show: Boolean, // modal showing if true, else if false modal hide
        close: Function, // close modal (show => false),
    }, 
    data() {
        return {
            mainDivisions: [],
            divisionTypes: [],
            divisionsForCascader: [],
            newDivision: {
                cascaderSelection: [], // Нужен только для каскэдера
                id_parent: "",
                id_main: "",
                id_type: "",
                full_name_ru: "",
                full_name_uz: "",
                short_name_ru: "",
                short_name_uz: "",
            }, 
            propsCascader:  {
                                lazy:true, 
                                checkStrictly: true,
                                async lazyLoad (node, resolve) {
                                    const {level} = node;
                                    const {data} = await fetchChildDivisions(node.value);
                                    const divs = data.childs.map(d => ({
                                        value:d.id,
                                        label: d.short_name_ru
                                    }));
                                    resolve(divs);
                                }
                            },
        }
    }, 
    created() {
        this.getDivisionTypes();
        this.getMainDivisions();
    }, 
    methods: {
        async getMainDivisions() 
        {
            // Получаем главные подразделения
            const {data} = await fetchChildDivisions(1);
            
            // Устанавливаем главные подразделения
            this.mainDivisions = [data.parent, ...data.childs]; 

          
            // Устанавливаем только самое вышестоящее подразделение для Каскадэра
            this.divisionsForCascader = [   {
                                            label: data.parent.short_name_ru, 
                                            value: data.parent.id, 
                                            children: [],
                                        }
                                    ];
          
          
        }, 
        async getDivisionTypes() 
        {
            const {data} = await fetchDivisionTypes();
            this.divisionTypes = data;
        }, 

        handleCascaderChange(value) {
            this.newDivision.id_parent = value.at(-1);
        }, 
        async addDivision() {
            const res = await createDivision(this.newDivision);
            if(!res.errors && res.status) 
            {
                this.$message({
                    message: 'Объект успешно добавлен!',
                    type: 'success',
                    duration: 5 * 1000,
                });
                try {
                    const conf = await this.$confirm(`Желаете добавить ещё один объект в это же подразделение?`, 'Объект успешно добавлен!', {
                        confirmButtonText:"Ок",
                        cancelButtonText:"Нет",
                        type:"info"
                    });
                    if(conf == "confirm")
                    {
                        this.newDivision.full_name_ru = "";
                        this.newDivision.full_name_uz = ""; 
                        this.newDivision.short_name_ru = ""; 
                        this.newDivision.short_name_uz = "";
                        return;
                    }
                }
                catch(e) 
                {
                    console.log(e);
                }
            }
            this.close();
            this.newDivision = {
                        cascaderSelection: [], // Нужен только для каскэдера
                        id_parent: "",
                        id_main: "",
                        id_type: "",
                        full_name_ru: "",
                        full_name_uz: "",
                        short_name_ru: "",
                        short_name_uz: ""
                    };
        }
    }
}



</script>