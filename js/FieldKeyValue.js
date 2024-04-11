// Field KeyValue
var template = pkp.Vue.compile(`<div class="pkpFormField pkpFormField--text pkpFormField--keyvalues" :class="classes">
			<form-field-label
				:controlId="controlId"
				:label="label"
				:localeLabel="localeLabel"
				:isRequired="isRequired"
				:requiredLabel="__('common.required')"
				:multilingualLabel="multilingualLabel"
			/>
			<div
				v-if="isPrimaryLocale && description"
				class="pkpFormField__description"
				v-strip-unsafe-html="description"
				:id="describedByDescriptionId"
			/>
			<div class="pkpFormField__control" :class="controlClasses">
                <table class="form form-striped">
                    <tr v-for="row in currentValue" :key="row._id">
                        <td>
                            <input class="pkpFormField__input pkpFormField--text__input pkpFormField--urn__input"
                                type="text"
                                v-model="row.key" />
                        </td>
                        <td>
                            <input class="pkpFormField__input pkpFormField--text__input pkpFormField--urn__input"
                                type="text"
                                v-model="row.value" />
                        </td>
						<td>
							<button
								class="pkpButton pkpFormField--urn__button"
								@click.prevent="removeRow(row)">
								Remove
							</button>
						</td>
                    </tr>
                </table>
				<button
					class="pkpButton pkpFormField--urn__button"
					@click.prevent="addRow">
					Add New
				</button>
				<field-error
					v-if="errors.length"
					:id="describedByErrorId"
					:messages="errors"
				/>
				</div>
			</div>
		</div>`);

pkp.Vue.component('field-keyvalues', {
	name: 'FieldKeyValue',
	extends: pkp.Vue.component('field-text'),
	props: {
		
	},
	methods: {
		addRow() {
            this.currentValue.push({
                'key': '',
                'value': '',
                '_id': Object.keys(this.currentValue).length,
            });
        },
		removeRow(row) {
			const index = this.currentValue.indexOf(row);
			this.currentValue.splice(index, 1);
		}
	},
	render: function(h) {
		return template.render.call(this, h);
	}
});