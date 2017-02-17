<template>
    <router-link v-if="category != null"
            :to="{name: 'module:' + content.module.category.slug, params: {website_id: website, category: category}}"
            class="btn ink-reaction btn-info">Modifier
    </router-link>
    <router-link v-else
                 :to="{name: 'module:' + content.module.category.slug, params: {website_id: website}}"
                 class="btn ink-reaction btn-info">Modifier
    </router-link>
</template>

<script type="text/babel">

    export default{
        name: 'user-list-post',
        props: {
            line: {
                default: 'default'
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
                category: null
            }
        },
        mounted(){
            if(this.content.data.db !== undefined && this.content.data.db instanceof Array && this.content.data.db.length == 1){
                if(this.content.data.db[0].type !== undefined && this.content.data.db[0].type == 'static' && this.content.data.db[0].value !== undefined && this.content.data.db[0].value instanceof Array && this.content.data.db[0].value.length == 1){
                    this.category = this.content.data.db[0].value[0];
                }
            }
        }
    }
</script>