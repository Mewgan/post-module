<template>
    <form class="form">
        <div class="form-group">
            <input type="text" class="form-control" v-model="content.name" id="content_name">
            <label for="content_name">Nom *</label>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" v-model="content.block" id="content_block">
            <label for="content_block">Bloc *</label>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" v-model="content.module.name" readonly id="content_module">
            <label for="content_module">Module</label>
        </div>
        <div>
            <div>
                <ul class="nav nav-tabs nav-justified" data-toggle="tabs">
                    <li :class="classStatic"><a href="#content_static">Statique</a></li>
                    <li :class="classDynamic"><a href="#content_dynamic">Dynamique</a></li>
                </ul>
            </div><!--end .card-head -->
            <div class="card-body tab-content">
                <div :class="[classStatic, 'tab-pane']" id="content_static">
                    <table class="table table-bordered no-margin">
                        <tbody>
                        <tr v-for="(db,i) in content.data.db">
                            <td>{{i}}</td>
                            <td>
                                <div class="form-group">
                                    <input type="text" v-model="db.table" class="form-control" :id="'db_table_'+i">
                                    <label :for="'db_table_'+i">Table</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" v-model="db.column" class="form-control" :id="'db_column_'+i">
                                    <label :for="'db_column_'+i">Colonne</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" v-model="db.value" class="form-control" :id="'db_value_'+i">
                                    <label :for="'db_value_'+i">Valeur</label>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn ink-reaction btn-floating-action btn-danger"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div :class="[classDynamic, 'tab-pane']" id="content_dynamic">
                    Coming soon !
                </div>
            </div><!--end .card-body -->
        </div>
    </form>
</template>

<script type="text/babel">

    import {AppVendor} from '../../../../../Blocks/AdminBlock/Resources/public/js/app'

    export default{
        name: 'list-post',
        props: {
            content: {
                type: Object,
                required: true
            },
            page: {
                required: true
            },
            website: {
                required: true
            }
        },
        computed: {
            classStatic: function () {
                return {
                    active: (!'data' in this.content || ('data' in this.content && 'type' in this.content.data && this.content.data.type == 'static'))
                }
            },
            classDynamic: function () {
                return {
                    active: ('data' in this.content && 'type' in this.content.data && this.content.data.type == 'dynamic')
                }
            }
        },
        methods: {

        },
        mounted () {
            this.$nextTick(function () {
                AppVendor()._initTabs();
                if(this.content.data.length == 0){
                    this.$set(this.content,'data', {
                        class: '',
                        type: 'static',
                        route_name: 'module:post.type:dynamic.action:read',
                        total_row: 10,
                        db: [],
                        link: []
                    });
                }
            });
        }
    }
</script>