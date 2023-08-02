<template>
    <el-dialog  title="Добавление подразделений" :visible.sync="this.show" width="65%" :before-close="close">
        <div class="form-container">
            <el-form ref="addChildDivisionForm" :model="updatingDivision" label-position="top" label-width="300px"  style="">
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="Вышестоящее подразделение">
                            <el-input v-model="updatingDivision.full_name_ru" disabled></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Главное подразделение(управление)" label-width="auto">
                            <el-select v-model="updatingDivision.id_main" placeholder="Главное управление" style="width: 100%;">
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
                            <el-select v-model="updatingDivision.id_type" placeholder="Тип подразделения"     style="width: 100%">
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
                            <el-input v-model="updatingDivision.short_name_ru"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Короткое название на узбекском" label-position="left">
                            <el-input v-model="updatingDivision.short_name_uz"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="Полное название на русском" label-position="left">
                            <el-input v-model="updatingDivision.full_name_ru" type="textarea" :autosize="{ minRows: 4, maxRows: 5}" ></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Полное название на узбекском" label-position="left">
                            <el-input v-model="updatingDivision.full_name_uz" type="textarea" :autosize="{ minRows: 4, maxRows: 5}" ></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>                    
            
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="update">
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

import {fetchChildDivisions, fetchDivisionTypes, updateDivision} from "@/api/divisions";



export default {
    props: {
        show: Boolean, // modal showing if true, else if false modal hide
        close: Function, // close modal (show => false),
        forParent: Object,
    }, 
    data() {
        return {
            mainDivisions: [],
            divisionTypes: [],
            updatingDivision: {...this.forParent}, 
        }
    }, 
    created() {
        this.getDivisionTypes();
        this.getMainDivisions();
        console.log(this.forParent);
    }, 
    methods: {
        async getMainDivisions() 
        {
            // Получаем главные подразделения
            const {data} = await fetchChildDivisions(1);
            
            // Устанавливаем главные подразделения
            this.mainDivisions = [data.parent, ...data.childs]; 

        }, 
        async getDivisionTypes() 
        {
            const {data} = await fetchDivisionTypes();
            this.divisionTypes = data;
        }, 


        async update() {
            const res = await updateDivision(this.updatingDivision.id, this.updatingDivision);
            if(!res.errors && res.status) 
            {
                this.$message({
                    message: 'Объект успешно добавлен!',
                    type: 'success',
                    duration: 5 * 1000,
                });
            }
            this.close();
        }
    }
}



</script>