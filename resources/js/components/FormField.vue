<template>
  <default-field :field="field">
    <template slot="field">
      <loading-button :loading="loading">
        <input
            class="w-full form-control form-input form-input-bordered"
            :value="value"
            :id="field.attribute"
            :disabled="true"
            :list="`${field.attribute}-list`"
        />
      </loading-button>
    </template>
  </default-field>
</template>

<script>
import {FormField, HandlesValidationErrors} from 'laravel-nova'
import debounce from 'lodash.debounce'

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field'],

  created() {
    for (const field of this.field.sourceFields) {
      Nova.$on(`${field}-change`, (value) => {
        this.fieldValues[field] = value
      })
    }

    this.debouncedCalculate = debounce(this.calculate, 400)
  },

  data() {
    return {
      loading: false,
      fieldValues: {...this.field.initialValues}
    }
  },

  watch: {
    fieldValues: {
      handler() {
        this.debouncedCalculate()
      },
      deep: true
    }
  },

  methods: {
    calculate() {
      this.loading = true;
      Nova.request()
          .post(
              this.field.calculateUrl,
              {
                resourceId: this.resourceId,
                fieldName: this.field.attribute,
                data: this.fieldValues
              }
          )
          .then(response => {
            this.value = response.data
            Nova.$emit(`${this.field.attribute}-change`, this.value)
            this.loading = false;
          })
          .catch(() => {
            this.loading = false;
          });
    },
    fill() {
      // Doesn't send any data
    },
  },
}
</script>
