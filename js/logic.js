( function( __, addRuleTypeCategory, addRuleType, getFormPreset ) {
	addRuleTypeCategory(
		'metabox',
		{
			label: __( 'Meta Box' )
		}
	);
	addRuleType(
		'metabox/archive-field',
		{
			label: __( 'Archive Field' ),
			category: 'metabox',
			form: getFormPreset( 'key-value' )
		}
	);
	addRuleType(
		'metabox/post-field',
		{
			label: __( 'Post Field' ),
			category: 'metabox',
			form: getFormPreset( 'key-value' )
		}
	);
	addRuleType(
		'metabox/post-author-field',
		{
			label: __( 'Post Author Field' ),
			category: 'metabox',
			form: getFormPreset( 'key-value' )
		}
	);
	addRuleType(
		'metabox/user-field',
		{
			label: __( 'User Field' ),
			category: 'metabox',
			form: getFormPreset( 'key-value' )
		}
	);
	addRuleType(
		'metabox/settings-page-field',
		{
			label: __( 'Settings Page Field' ),
			category: 'metabox',
			form: function( props ) {
				var operator = props.rule.operator
				return {
					key: {
						type: 'text',
						placeholder: 'Key',
					},
					operator: {
						type: 'operator',
						operators: [
							'equals',
							'does_not_equal',
							'is_set',
						],
					},
					compare: {
						type: 'text',
						placeholder: 'Value',
						visible: 'is_set' !== operator,
					},
					option_name: {
						type: 'text',
						placeholder: 'Option name',
					},
				}
			}
		}
	);
} )( BBLogic.i18n.__, BBLogic.api.addRuleTypeCategory, BBLogic.api.addRuleType, BBLogic.api.getFormPreset );
