<template>
  <button
      class="btn-status"
      :class="statusOn ? 'btn-status-on' : 'btn-status-off'"
      type="button"
      @click="$parent.$emit('toggle-status', model)"
  ></button>
</template>

<script>
export default {
  props: {
    model: {
      type: Object,
      required: true,
    },
  },
  computed: {
    statusOn() {
      if (typeof (model.status_translated) !== 'undefined') {
        return model.status_translated === 1;
      } else {
        return model.status === 1;
      }
    },
  },
};
</script>
