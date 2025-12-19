<template>

  <b-toast
      v-model="isShow"
      :variant="getType"
      :title="getType === 'warning' || getType === 'danger' ? 'Внимание' : 'Информация'"
      solid
  >
    {{ getMessage }}
  </b-toast>

</template>

<script>

export default {
  name: "Messages",
  data() {
    return {
    }
  },
  computed: {
    isShow: {
      get(){
        return this.$store.state.MESSAGES.isShow
      },
      set(value){
        return this.$store.commit('MESSAGES/SET_IS_SHOW', value)
      }
    },
    getMessage(){
      return this.$store.state.MESSAGES.message
    },
    getTime(){
      return this.$store.state.MESSAGES.time
    },
    getType(){
      return this.$store.state.MESSAGES.type
    },
    getTop(){
      return this.$store.state.MESSAGES.isTop
    },
    getBottom(){
      return this.$store.state.MESSAGES.isBottom
    },
    getRight(){
      return this.$store.state.MESSAGES.isRight
    },
    getLeft(){
      return this.$store.state.MESSAGES.isLeft
    },
  }
}
</script>

<style scoped>

</style>