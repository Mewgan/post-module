<template>
    <section>
        <p>Hello</p>
    </section>
</template>

<script type="text/babel">
    export default{
        name: 'single-post',
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
        }
    }
</script>