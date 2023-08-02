<template>
    <div class="app-container">
        <h1>Справочник подразделений/объектов</h1>
        <el-button 
                type="success"  
                icon="el-icon-plus" 
                style="float: right; margin-right: 12px;" 
                @click="handleOpenAddDivsionModal"
            >Добавить подразделение</el-button>
        <el-table   
                lazy
                row-key="id"
                :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
                :load="getDivisionChilds"
                ref="divTable" 
                :data="divisionsList"  
                fit highlight-current-row border 
                style="width: 100%;"
        >
            <el-table-column label="ID" :min-width="11" prop="id"></el-table-column>
            <el-table-column prop="short_name_ru" label="Название подразделения (кратко)" :min-width="20"></el-table-column>
            <el-table-column prop="full_name_ru" label="Название подразделения (полное)" :min-width="59"></el-table-column>
            <el-table-column prop="" label="Доп. ф-ии" :min-width="10">
                <template slot-scope="scope">
                    <div style="text-align: center;">
                        <el-button type="success" icon="el-icon-plus" circle @click="handleOpenAddChildDivisionModal(scope.row)"></el-button>
                        <el-button type="info" icon="el-icon-edit" circle @click="handleOpenEditDivisionModal(scope.row)"></el-button>
                        <el-button type="danger" icon="el-icon-delete" circle @click="removeDivision(scope.row.id)"></el-button>
                    </div>
                </template>
            </el-table-column>
        </el-table>

        <AddDivsion :show="showAddDivisionModal" :close="handleCloseAddDivsionModal"></AddDivsion>
        <AddChildDivision v-if="selectedDiv" :show="showAddChildDivisionModal" :close="handleCloseAddChildDivisionModal" :forParent="selectedDiv"></AddChildDivision>
        <EditDivision v-if="selectedDiv" :show="showEditDivisionModal" :close="handleCloseEditDivisionModal" :forParent="selectedDiv"></EditDivision>
    </div>
</template>



<script>

import {fetchChildDivisions, deleteDivision } from "@/api/divisions";
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import AddChildDivision from "./components/AddChildDivision.vue";
import AddDivsion from "./components/AddDivsion.vue";
import EditDivision from "./components/EditDivision.vue";

export default { 

    data() {
        return {
            showAddDivisionModal: false, // Модальное окно добавления подразделения любого уровня
            showAddChildDivisionModal: false,  // Модальное окно добавления подразделения следующего уровня
            showEditDivisionModal: false, // Модальное окно редактирования подразделения
            
            selectedDiv: null, // Выбранное подразделение
            divisionsList: [],
        }
    }, 
    created() {
        this.getMainDivisions();
    }, 
    methods: {


        // Получение подразделений первого уровня (Главных подразделений)
        async getMainDivisions() {
            const {data} = await fetchChildDivisions(1);
            this.divisionsList = data.childs.map(d => ({...d, hasChildren:true})); 
        }, 
        // Получение подразделений следующих уровней 
        async getDivisionChilds(tree, treeNode, resolve)
        {
            const divId = tree.id;
            const {data} = await fetchChildDivisions(divId);
            const childs = data.childs.map(d => ({...d, hasChildren:true}));
           
            resolve(childs); 
        }, 



        async removeDivision(divisionId)
        {
            try {
                let conf = await this.$confirm(`Вы точно хотите удалить это подразделение/объект? Все подразделения/объекты и связанные данные с этим подразделением могут быть утеряны.`, 'Внимание!', {type:"warning"});
                if(conf != "confirm")
                    return;
            }
            catch(e)
            {
                console.log(e);
                return;
            }
            const res = await deleteDivision(divisionId);
            console.log(res);



        },


        // START Модальное окно добавления Подразделения ЛЮБОГО УРОВНЯ
            handleOpenAddDivsionModal() {
                this.showAddDivisionModal = true;
            },
            handleCloseAddDivsionModal() {
                this.showAddDivisionModal = false;
            },
        // END Модальное окно добавления Подразделения ЛЮБОГО УРОВНЯ

        // START Модальное окно добавления подразделения для следующего уровня
            handleOpenAddChildDivisionModal(division) {
                this.selectedDiv = division;
                console.log(division);
                this.showAddChildDivisionModal = true;
            },
            handleCloseAddChildDivisionModal() {
                this.selectedDiv = null;
                this.showAddChildDivisionModal = false;
            },
        // END Модальное окно добавления подразделения для следующего уровня

        // START Модальное окно редактирования подразделения
            handleOpenEditDivisionModal(division) {
                this.selectedDiv = division;
                this.showEditDivisionModal = true;
            }, 
            handleCloseEditDivisionModal()
            {
                this.selectedDiv = "";
                this.showEditDivisionModal = false;
            },
        // End Модальное окно редактирования подразделения


    }, 
    components: { Pagination, AddDivsion, AddChildDivision, EditDivision }


}

</script>