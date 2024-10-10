(function(wp) {

	wp.blocks.registerBlockType('r34ics/r34ics-block', {
		title: wp.i18n.__('ICS Calendar'),
		
		description: wp.i18n.__('Embed a calendar feed.'),

		icon: 'calendar-alt',

		category: 'embed',

		supports: {
			html: false,
		},

		edit: function(props) {

			function onChangeURL(newContent) {
				props.setAttributes({ r34ics_url: newContent });
			}
			function onChangeTitle(newContent) {
				props.setAttributes({ r34ics_title: newContent });
			}
			function onChangeDescription(newContent) {
				props.setAttributes({ r34ics_description: newContent });
			}
			function onChangeView(newContent) {
				props.setAttributes({ r34ics_view: newContent });
			}

			return wp.element.createElement(
				'div',
				{ className: 'r34ics' },
				[
					wp.element.createElement(
						wp.components.TextControl,
						{
							className: 'r34ics_url',
							onChange: onChangeURL,
							label: wp.i18n.__('ICS Calendar URL'),
							placeHolder: wp.i18n.__('Enter ICS calendar URL'),
							value: props.attributes.r34ics_url
						}
					),
					wp.element.createElement(
						wp.editor.InspectorControls,
						{},
						wp.element.createElement(
							wp.components.TextControl,
							{
								className: 'r34ics_title',
								onChange: onChangeTitle,
								label: wp.i18n.__('Title'),
								placeHolder: wp.i18n.__('Enter title or "none" to omit...'),
								value: props.attributes.r34ics_title
							}
						),
					),
					wp.element.createElement(
						wp.editor.InspectorControls,
						{},
						wp.element.createElement(
							wp.components.TextControl,
							{
								className: 'r34ics_description',
								onChange: onChangeDescription,
								label: wp.i18n.__('Description'),
								placeHolder: wp.i18n.__('Enter description or "none" to omit...'),
								value: props.attributes.r34ics_description
							}
						),
					),
					wp.element.createElement(
						wp.editor.InspectorControls,
						{},
						wp.element.createElement(
							wp.components.SelectControl,
							{
								className: 'r34ics_view',
								onChange: onChangeView,
								label: wp.i18n.__('View'),
								options: [
									{ label: 'Month', value: 'month' },
									{ label: 'List', value: 'list' },
									{ label: 'Current Week', value: 'currentweek' },
									{ label: 'Custom', value: '' },
								],
								value: props.attributes.r34ics_view
							}
						),
					),
				]
			);
		},

		save: function(props) {
			return wp.element.createElement('div', {}, props.attributes.r34ics_url);
		}
	});
})(
	window.wp
);
