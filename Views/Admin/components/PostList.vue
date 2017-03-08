<style>
    .list-post .breadcrumb {
        display: inline-block;
    }

    .list-post button {
        margin-left: 10px;
    }
    .list-post .section-header .btn{
        margin-left: 10px;
    }
    .list-post .section-body {
        margin-top: 10px;;
    }
    .list-post .checkbox {
        float: left;
    }
    .list-post .list-results .post-box {
        padding: 20px 10px;
    }
    .list-post a.pointer{
        cursor: pointer;
    }
    .list-post .post-icon {
        padding: 0 10px;
        display: inline-block;
        vertical-align: middle;
    }
    .list-post .category-icon{
        padding-right: 10px;
    }
    .list-post .clearfix{
        clear: both;
    }
    .list-post .edit-category{
        cursor: pointer;
        position: absolute;
        top: 0;
        right: 5px;
    }
    .list-post .edit-category i{
        margin-right: 10px;
    }
    .list-post .edit-category:hover i{
        font-size:2em;
    }
    .list-post .category-title{
        padding-right:20px;
    }
    .list-post .post-change-state{
        float: left;
        margin: 10px 20px;
    }
</style>

<template>
    <section class="list-post">
        <div class="section-header">
            <ol class="breadcrumb">
                <li class="active">Articles</li>
            </ol>
            <router-link :to="{name: 'module:post:action', params: {website_id: website_id, post_id: 'create'}}"
                         class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                <i class="fa fa-plus" aria-hidden="true"></i> Ajouter un article
            </router-link>
            <button data-toggle="modal" data-target="#createPostCategoryModal"
                    class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Ajouter une catégorie
            </button>
        </div>
        <div class="section-body">

            <div class="alert alert-info" role="alert">
                <strong><i class="fa fa-info-circle"></i> Sur cette page vous avez la possibilité de :</strong><br/>
                <p>- de créer, modifier ou supprimer un article</p>
                <p>- de publier ou dépublier un article</p>
                <p>- de créer, modifier ou supprimer une catégorie</p>
            </div>

            <div class="card tabs-left style-default-light">

                <!-- BEGIN SEARCH BAR -->
                <div class="card-body style-primary no-y-padding">
                    <form class="form form-inverse search-item" @submit.prevent.stop="search">
                        <div class="form-group">
                            <div class="input-group input-group-lg">
                                <div class="input-group-content">
                                    <input type="text" class="form-control" id="searchInput" v-model="search_value"
                                           placeholder="Recherche">
                                    <div class="form-control-line"></div>
                                </div>
                                <div class="input-group-btn">
                                    <button class="btn btn-floating-action btn-default-bright" @click="search"
                                            type="button"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div><!--end .form-group -->
                    </form>
                </div><!--end .card-body -->
                <!-- END SEARCH BAR -->

                <!-- BEGIN TAB RESULTS -->
                <ul class="card-head nav nav-tabs tabs-accent post-categories" data-toggle="tabs">
                    <li class="post-category post-all active"><a @click="refresh(resource.name);addClass('all')">Tous
                        les
                        articles</a></li>
                    <li class="post-category post-nothing">
                        <a @click="setParams({resource:resource.name, key: 'filter', value: {column:'c.id', operator:'isNull'}});addClass('nothing')">Pas
                            de catégorie</a>
                    </li>
                    <li :class="'post-category post-' + category.slug" v-for="category in categories">
                        <a class="pointer"
                           @click="setParams({resource:resource.name, key: 'filter', value: {column:'c.id',operator:'eq',value:category.id}});addClass(category.slug)">
                            <i :title="getIconTitle('Cette catégorie',category.website)"
                               :class="'category-icon ' + getIconClass(category.website)"></i>
                            <span class="category-title">{{category.name}}</span>
                        </a>
                        <span @click="selectCategory(category)" data-toggle="modal" data-target="#editPostCategoryModal"
                              class="pull-right clearfix edit-category"><i class="fa fa-pencil"></i></span>
                    </li>
                </ul>
                <!-- END TAB RESULTS -->

                <!-- BEGIN TAB CONTENT -->
                <div class="card-body tab-content style-default-bright">
                    <div class="tab-pane active">
                        <div class="row">
                            <div class="col-lg-12">

                                <!-- BEGIN PAGE HEADER -->
                                <div class="margin-bottom-xxl">
                                    <label class="text-light text-lg">Lister : </label>
                                    <select v-model.number="resource.max">
                                        <option v-for="option in max_options" :value="option">{{option}}
                                        </option>
                                    </select>
                                    <span class="text-light text-lg">Total <strong>{{resource.total}}</strong></span>
                                    <div class="btn-group btn-group-sm pull-right">
                                        <button type="button" class="btn btn-default-light dropdown-toggle"
                                                data-toggle="dropdown">
                                            <span class="glyphicon glyphicon-arrow-down"></span> Trier
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right animation-dock" role="menu">
                                            <li>
                                                <a @click="setParams({resource: resource.name, key: 'order', value: {column:'p.updated_at',dir:'asc'}});addClass('all')">Date
                                                    croissant</a></li>
                                            <li>
                                                <a @click="setParams({resource: resource.name, key: 'order', value: {column:'p.updated_at',dir:'desc'}});addClass('all')">Date
                                                    décroissant</a></li>
                                            <li>
                                                <a @click="setParams({resource: resource.name, key: 'order', value: {column:'p.title',dir:'asc'}});addClass('all')">Titre
                                                    croissant</a></li>
                                            <li>
                                                <a @click="setParams({resource: resource.name, key: 'order', value: {column:'p.title',dir:'desc'}});addClass('all')">Titre
                                                    décroissant</a></li>
                                        </ul>
                                    </div>
                                    <div class="btn-group btn-group-sm pull-right">
                                        <button type="button" class="btn btn-default-light dropdown-toggle"
                                                data-toggle="dropdown">
                                            <span class="glyphicon glyphicon-arrow-down"></span> Séléction
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right animation-dock" role="menu">
                                            <li><a @click="updatePostState(1)">Publier</a></li>
                                            <li><a @click="updatePostState(0)">Dépubier</a></li>
                                            <li><a data-toggle="modal" data-target="#deletePostModal">Supprimer</a></li>
                                        </ul>
                                    </div>
                                </div><!--end .margin-bottom-xxl -->
                                <!-- END PAGE HEADER -->

                                <!-- BEGIN RESULT LIST -->
                                <div class="list-results list-results-underlined">
                                    <div v-for="post in resource.data" class="col-xs-12 post-box">
                                        <div v-if="post.website.id == website_id" class="checkbox checkbox-styled checkbox-primary">
                                            <label>
                                                <input type="checkbox" :value="post.id" v-model="selected_items">
                                                <span></span>
                                            </label>
                                        </div>
                                        <img v-if="post.thumbnail != null" class="pull-left width-3"
                                             v-img="post.thumbnail.path"
                                             :alt="post.thumbnail.alt"/>
                                        <div>
                                            <router-link
                                                    :to="{name: 'module:post:action', params:{website_id: website_id, post_id: post.id}}"
                                                    class="text-medium text-lg text-primary">{{post.title}}
                                            </router-link>
                                            <div class="pull-right">
                                                <div class="post-change-state" v-if="post.website.id == website_id">
                                                    <div class="switch">
                                                        <label>
                                                            Brouillon
                                                            <input @click="selectPost(post);updatePostState(!post.published)"
                                                                   :checked="post.published" type="checkbox">
                                                            <span class="lever"></span>
                                                            Publié
                                                        </label>
                                                    </div>
                                                </div>
                                                <router-link
                                                        :to="{name: 'module:post:action', params:{website_id: website_id, post_id: post.id}}"
                                                        class="btn ink-reaction btn-floating-action btn-info"><i
                                                        class="fa fa-pencil"></i></router-link>
                                                <a @click="selectPost(post)" data-toggle="modal"
                                                   data-target="#deletePostModal"
                                                   class="btn ink-reaction btn-floating-action btn-danger"><i
                                                        class="fa fa-trash"></i></a>
                                                <span v-show="auth.status.level < 4" class="post-icon"><i
                                                        :title="getIconTitle('Cet article', post.website)"
                                                        :class="getIconClass(post.website)"></i> </span>
                                            </div>
                                            <br/>
                                        </div>
                                        <div class="contain-xs pull-left">
                                            {{post.description}}
                                        </div>
                                        <div class="clearfix"></div>
                                        <em class="pull-right">Dernière mise à jour : {{ post.updated_at.date |
                                            moment('DD/MM/YYYY') }}</em>
                                    </div><!--end .col -->
                                </div><!--end .list-results -->
                                <!-- END RESULTS LIST -->

                                <!-- BEGIN PAGING -->
                                <pagination :refresh="refresh_items" :resource="resource"></pagination>
                                <!-- END PAGING -->

                            </div><!--end .col -->
                        </div><!--end .row -->
                    </div><!--end .tab-pane -->
                </div><!--end .card-body -->
                <!-- END TAB CONTENT -->

            </div><!--end .card -->
        </div><!--end .section-body -->

        <div class="modal fade" id="deletePostModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="deletePostModalLabel">Suppression</h4>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer cet article ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="deletePost()">Oui
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="createPostCategoryModal" tabindex="-1" role="dialog"
             aria-labelledby="formModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="createFormModalLabel">Ajouter une catégorie</h4>
                    </div>
                    <form class="form" role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input type="text" v-model="new_category" id="category" class="form-control">
                                        <label for="category" class="control-label">Titre</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            <button type="button" @click="createCategory" data-dismiss="modal" class="btn btn-primary">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <div class="modal fade" id="editPostCategoryModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="editFormModalLabel">Modifier la catégorie</h4>
                    </div>
                    <form class="form" role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input type="text" v-model="category.name" id="edit-category"
                                               class="form-control">
                                        <label for="edit-category" class="control-label">Titre</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="deleteCategory" data-dismiss="modal" class="btn btn-danger">
                                Supprimer la catégorie
                            </button>
                            <button type="button" @click="updateCategory" data-dismiss="modal" class="btn btn-primary">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </section>

</template>


<script type="text/babel">

    import {mapGetters, mapActions} from 'vuex'
    import {post_api, post_category_api} from '../api'
    import pagination_mixin from '@front/mixin/pagination'

    export default
    {
        components: {
            Pagination: resolve => { require(['@front/components/Helper/Pagination.vue'], resolve) }
        },
        mixins: [pagination_mixin],
        data () {
            return {
                website_id: this.$route.params.website_id,
                resource: {
                    url: post_api.all + this.$route.params.website_id,
                    name: 'posts_' + this.$route.params.website_id,
                    data: [],
                    max: 10,
                    total: 0
                },
                category: {},
                new_category: '',
                categories: {},
                search_value: '',
                selected_items: [],
                refresh_items: false,
                icon_class: '',
                max_options: [10, 20, 30]
            }
        },
        computed: {
            ...mapGetters(['auth'])
        },
        methods: {
            ...mapActions([
                'create', 'read', 'update', 'destroy', 'refresh', 'setParams', 'deleteResources'
            ]),
            search () {
                if (this.search_value === '') {
                    if (this.resource.name !== undefined) this.refresh(this.resource.name);
                } else {
                    this.setParams({
                        resource: this.resource.name,
                        key: 'search',
                        value: this.search_value
                    });
                    this.addClass('all');
                }
            },
            addClass (slug){
                $('.post-category').removeClass('active');
                $('.post-' + slug).addClass('active');
            },
            selectPost (post){
                this.selected_items = [post.id];
            },
            updatePostState (state) {
                if (this.selected_items.length > 0) {
                    if (state == true || state == 'true') state = 1;
                    if (state == false || state == 'false') state = 0;
                    this.update({
                        api: post_api.change_state + this.website_id,
                        value: {
                            state: parseInt(state),
                            ids: this.selected_items
                        }
                    }).then((response) => {
                        this.selected_items = [];
                        if (response.data.status == 'success')
                            this.refresh_items = !this.refresh_items;
                    });
                }
            },
            deletePost () {
                if (this.selected_items.length > 0) {
                    this.deleteResources({
                        api: post_api.destroy + this.website_id,
                        resource: this.resource.name,
                        ids: this.selected_items
                    }).then(() => {
                        this.selected_items = [];
                    });
                }
            },
            selectCategory (category) {
                this.category = category;
            },
            createCategory () {
                if (this.new_category != '') {
                    this.create({
                        api: post_category_api.create + this.website_id,
                        value: {name: this.new_category}
                    }).then(() => {
                        this.loadCategory();
                    });
                }
            },
            updateCategory(){
                if (this.category.name != '') {
                    this.update({
                        api: post_category_api.update + this.category.id + '/' + this.website_id,
                        value: {name: this.category.name}
                    }).then(() => {
                        this.loadCategory();
                        this.refresh_items = !this.refresh_items;
                    });
                }
            },
            deleteCategory(){
                this.destroy({
                    api: post_category_api.destroy + this.website_id,
                    ids: [this.category.id]
                }).then(() => {
                    this.loadCategory();
                });
            },
            loadCategory(){
                return this.read({api: post_category_api.list_names + this.website_id}).then((response) => {
                    if (response.data.resource !== undefined)
                        this.categories = response.data.resource;
                })
            }
        },
        mounted () {

            this.loadCategory().then(() => {
                if (this.$route.params.category !== undefined) {
                    this.setParams({
                        resource: this.resource.name,
                        key: 'filter',
                        value: {column: 'c.id', operator: 'eq', value: this.$route.params.category}
                    });
                    let index = this.categories.findIndex((i) => i.id == this.$route.params.category);
                    if (this.categories[index].slug !== undefined) this.addClass(this.categories[index].slug);
                }
            });
        }
    }
</script>
