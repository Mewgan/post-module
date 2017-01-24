<style>
    .module-title{
        padding: 10px;
        background: #f2f2f2;
    }
    .edit-list-post .add-field{
        margin-top: -20px;
        margin-right: 8px;
    }
    .edit-list-post .edit-route-btn{
        margin-top: -22px !important;
        margin-right: 10px !important;
    }
</style>
<template>
    <div class="edit-list-post">
        <form class="form">
            <h5 class="module-title">Information :</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" v-model="content.name" :id="'content-name-' + line">
                        <label :for="'content-name-' + line">Nom *</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" v-model="content.block" :id="'content-block-' + line">
                        <label :for="'content-block-' + line">Bloc *</label>
                    </div>
                </div>
            </div>
            <div v-show="auth.status.level < 4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" :value="content.module.category.title" readonly
                                   :id="'content-module-' + line">
                            <label :for="'content-module-' + line">Module</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" :value="content.module.name" readonly
                                   :id="'content-extension-' + line">
                            <label :for="'content-extension-' + line">Extension</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="number" class="form-control" v-model="content.data.total_row" :id="'content-total-row-' + line">
                        <label :for="'content-total-row-' + line">Nombre max d'articles à afficher</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" >
                        <input type="text" class="form-control" v-model="content_data.class" :id="'content-class-' + line">
                        <label :for="'content-class-' + line">Class</label>
                    </div>
                </div>
            </div>
            <h5 class="module-title">Choix du template :</h5>
            <template-editor :id="line" :templates="templates" :template="content.template" label="Template du contenu"></template-editor>
            <div>
                <h5 class="module-title">Configuration avancé :</h5>
                <div class="form-group" v-if="page != null && 'route' in page && 'url' in page.route">
                    <input type="text" class="form-control" id="page_url" :value="page.route.url" readonly><div class="form-control-line"></div>
                    <label for="page_url">Page url</label>
                </div>
                <br>
                <table class="table table-bordered no-margin">
                    <tbody>
                    <tr v-for="(db,i) in content_data.db">
                        <td style="width: 5%">{{i}}</td>
                        <td style="width: 30%">
                            <div class="form-group">
                                <select :id="'db_table_'+i" v-model="db.alias" class="form-control">
                                    <option v-for="(table,alias) in tables" :value="alias">{{table}}</option>
                                </select>
                                <label :for="'db_table_'+i">Table</label>
                            </div>
                        </td>
                        <td style="width: 30%">
                            <div>
                                <ul class="nav nav-tabs nav-justified" data-toggle="tabs">
                                    <li :class="classDbStatic(i)"><a @click="changeDbType(i,'static')" :href="'#db_static'+i">Statique</a></li>
                                    <li :class="classDbDynamic(i)"><a @click="changeDbType(i,'dynamic')" :href="'#db_dynamic'+i">Dynamique</a></li>
                                </ul>
                            </div><!--end .card-head -->
                            <div class="card-body tab-content">
                                <div :class="[classDbStatic(i), 'tab-pane']" :id="'db_static'+i">
                                    <select2 v-if="getValues(db.alias) instanceof Array && getValues(db.alias).length > 0" @updateValue="updateDbValue" :updateParams="{key: i}"
                                             :contents="getValues(db.alias)" :id="'db-value-' + line + '-' + i" val_index="id" index="name" label="Valeur"
                                             :val="db.value"></select2>
                                </div>
                                <div :class="[classDbDynamic(i), 'tab-pane']" :id="'db_dynamic'+i">
                                    <div class="form-group">
                                        <input type="text" v-model="db.route" class="form-control" :id="'db_route_'+i">
                                        <label :for="'db_route_'+i">Route</label>
                                    </div>
                                    <div class="form-group">
                                        <select :id="'db_column_'+i" v-model="db.column" class="form-control">
                                            <option v-for="column in columns" :value="column">{{column}}</option>
                                        </select>
                                        <label :for="'db_column_'+i">Colonne</label>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="width: 5%">
                            <button type="button" @click="removeDbField(i)" class="btn ink-reaction btn-floating-action btn-danger"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <button type="button" @click="addDbField" class="btn ink-reaction pull-right btn-floating-action btn-info add-field"><i class="fa fa-plus"></i></button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h5 class="module-title">Url d'un article :</h5>
                    <route-editor :line="line" :website_id="website" :route="route" @updateRoute="updateRoute"></route-editor>
                </div>
            </div>
            <div>
                <h5 class="module-title">Configuration des liens d'articles :</h5>
                <table class="table table-bordered no-margin">
                    <tbody>
                    <tr v-for="(link,i) in content_data.link">
                        <td style="width: 5%">{{i}}</td>
                        <td style="width: 30%">
                            <div class="form-group">
                                <input type="text" v-model="link.route" class="form-control" :id="'link_route_'+i">
                                <label :for="'link_route_'+i">Route</label>
                            </div>
                        </td>
                        <td style="width: 30%">
                            <div class="form-group">
                                <select :id="'link_table_'+i" v-model="link.alias" class="form-control">
                                    <option v-for="(table,alias) in tables" :value="alias">{{table}}</option>
                                </select>
                                <label :for="'link_table_'+i">Table</label>
                            </div>
                        </td>
                        <td style="width: 30%">
                            <div>
                                <ul class="nav nav-tabs nav-justified" data-toggle="tabs">
                                    <li :class="classLinkStatic(i)"><a @click="changeLinkType(i,'static')" :href="'#link_static'+i">Statique</a></li>
                                    <li :class="classLinkDynamic(i)"><a @click="changeLinkType(i,'dynamic')" :href="'#link_dynamic'+i">Dynamique</a></li>
                                </ul>
                            </div><!--end .card-head -->
                            <div class="card-body tab-content">
                                <div :class="[classLinkStatic(i), 'tab-pane']" :id="'link_static'+i">
                                    <div class="form-group">
                                        <select :id="'link_column_'+i" v-model="link.column" class="form-control">
                                            <option v-for="column in columns" :value="column">{{column}}</option>
                                        </select>
                                        <label :for="'link_column_'+i">Colonne</label>
                                    </div>
                                    <div class="form-group">
                                        <select :id="'link_value_'+i" @change="setValueId(i,link.alias,link.column,link.value)" v-model="link.value" class="form-control">
                                            <option v-for="value in getValues(link.alias)" :value="value[link.column]">{{value.name}}</option>
                                        </select>
                                        <label :for="'link_value_'+i">Valeur</label>
                                    </div>
                                </div>
                                <div :class="[classLinkDynamic(i), 'tab-pane']" :id="'link_dynamic'+i">
                                    <div class="form-group">
                                        <select :id="'link_column_'+i" v-model="link.column" class="form-control">
                                            <option v-for="column in columns" :value="column">{{column}}</option>
                                        </select>
                                        <label :for="'link_column_'+i">Colonne</label>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="width: 5%">
                            <button type="button" @click="removeLinkField(i)" class="btn ink-reaction btn-floating-action btn-danger"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <button type="button" @click="addLinkField(content_data.link.length)" class="btn ink-reaction pull-right btn-floating-action btn-info add-field"><i class="fa fa-plus"></i></button>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            <button type="button" @click="updateContent" class="btn btn-primary">Enregistrer</button>
        </div>
    </div>
</template>

<script type="text/babel">

    /* JS*/
    import RouteEditor from '../../../../../Blocks/AdminBlock/Front/components/Helper/RouteEditor.vue'
    import TemplateEditor from '../../../../../Blocks/AdminBlock/Front/components/Helper/TemplateEditor.vue'
    import Select2 from '../../../../../Blocks/AdminBlock/Front/components/Helper/Select2.vue'

    import {AppVendor} from '../../../../../Blocks/AdminBlock/Resources/public/js/app'
    import {mapActions, mapGetters} from 'vuex'
    import {route_api, template_api} from '../../../../../Blocks/AdminBlock/Front/api'
    import {post_api} from '../api'

    export default{
        name: 'list-post',
        components: {RouteEditor, TemplateEditor, Select2},
        props: {
            line: {
                default: '0'
            },
            content: {
                type: Object,
                required: true
            },
            page: {
                default: null
            },
            website: {
                required: true
            }
        },
        data(){
            return {
                content_data: {
                    class: '',
                    route_name: 'module:post.type:dynamic.action:read',
                    total_row: 10,
                    db: [],
                    link: []
                },
                tables: {
                    p: 'Article',
                    c: 'Catégorie'
                },
                columns: ['', 'id', 'slug'],
                values: {
                    c: null,
                    p: null
                },
                route: {},
                templates: []
            }
        },
        watch: {
            'content_data': {
                handler(){
                    this.$set(this.content, 'data', this.content_data);
                },
                deep: true
            }
        },
        computed: {
            ...mapGetters(['auth'])
        },
        methods: {
            ...mapActions(['read']),
            setValueId(key, table, col, val){
                if (table in this.values && this.values[table] != null) {
                    let index = this.values[table].findIndex((i) => i[col] == val);
                    this.content_data.link[key].value_id = this.values[table][index].id
                }
            },
            updateDbValue(val, oldVal, params){
                this.content_data.db[params.key].value = val;
            },
            classDbStatic (i) {
                return {
                    active: (i in this.content_data.db && 'type' in this.content_data.db[i] && this.content_data.db[i].type == 'static')
                }
            },
            classDbDynamic (i) {
                return {
                    active: (i in this.content_data.db && 'type' in this.content_data.db[i] && this.content_data.db[i].type == 'dynamic')
                }
            },
            classLinkStatic (i) {
                return {
                    active: (i in this.content_data.link && 'type' in this.content_data.link[i] && this.content_data.link[i].type == 'static')
                }
            },
            classLinkDynamic (i) {
                return {
                    active: (i in this.content_data.link && 'type' in this.content_data.link[i] && this.content_data.link[i].type == 'dynamic')
                }
            },
            updateRoute(route){
                this.route = route;
                this.content_data.route_name = route.name;
            },
            addDbField(){
                this.content_data.db.push({
                    alias: '',
                    type: 'static',
                    column: '',
                    route: '',
                    value: []
                });
                this.$nextTick(function () {
                    AppVendor()._initTabs();
                });
            },
            removeDbField(index){
                this.content_data.db.splice(index, 1);
            },
            addLinkField(){
                this.content_data.link.push({
                    alias: '',
                    type: 'static',
                    route: '',
                    column: '',
                    value: '',
                    value_id: ''
                });
                this.$nextTick(function () {
                    AppVendor()._initTabs();
                });
            },
            removeLinkField(index){
                this.content_data.link.splice(index, 1);
            },
            getValues(table){
                return this.values[table];
            },
            changeDbType(i, type){
                this.content_data.db[i].type = type;
            },
            changeLinkType(i, type){
                this.content_data.link[i].type = type;
            },
            updateContent(){
                if ('id' in this.content.template && this.content.template.id != '') {
                    this.$emit('updateContent', this.content);
                    this.closeModal();
                } else
                    this.setResponse({status: 'error', message: 'Veuillez choisir le template'});
            },
            closeModal(){
                $("#editContentModal" + this.line).modal("hide")
            }
        },
        created () {
            this.read({api: route_api.find_by_name + this.content_data.route_name}).then((response) => {
                this.route = response.data.resource;
            });
            this.read({api: template_api.get_website_content_layouts + this.website}).then((response) => {
                this.templates = response.data;
            });
            this.read({api: post_api.list_table_values + this.website}).then((response) => {
                this.values = response.data;
            });
        },
        mounted () {
            if (this.content.data != null && 'db' in this.content.data) this.content_data = this.content.data;

            this.$nextTick(function () {
                AppVendor()._initTabs();
            });
        }
    }
</script>