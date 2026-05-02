/* global jQuery */

(($) => {
	'use strict';

	const strings = {
		keywordLabelPlaceholder: 'Label',
		keywordUrlPlaceholder: 'URL (ex: /services/ ou #ancre)',
		removeLabel: 'Supprimer',
		qaQuestionLabel: 'Question',
		qaAnswerLabel: 'Réponse',
		heroMediaTitle: 'Choisir une image de fond',
		contactMediaTitle: 'Choisir une photo de contact',
		mediaButtonText: 'Utiliser cette image',
		...(window.kiyoseMetaBoxes || {}),
	};

	const mediaFrames = {};

	const trimmedValue = ($field) => String($field.val() || '').trim();

	const serializeKeywords = () => {
		const keywords = [];

		$('#kiyose_welcome_keywords_list .keyword-row')
			.toArray()
			.forEach((row) => {
				const $row = $(row);
				const label = trimmedValue($row.find('.kw-label'));
				const url = trimmedValue($row.find('.kw-url'));

				if (label) {
					keywords.push({ label, url });
				}
			});

		$('#kiyose_welcome_keywords').val(JSON.stringify(keywords));
	};

	const serializeQA = () => {
		const items = [];

		$('#kiyose_content1_qa_list .qa-row')
			.toArray()
			.forEach((row) => {
				const $row = $(row);
				const question = trimmedValue($row.find('.qa-question'));
				const answer = trimmedValue($row.find('.qa-answer'));

				if (question || answer) {
					items.push({ question, answer });
				}
			});

		$('#kiyose_content1_qa').val(JSON.stringify(items));
	};

	const createKeywordRow = () =>
		$('<div>', {
			class: 'kiyose-repeater-row kiyose-repeater-row--keyword keyword-row',
		}).append(
			$('<input>', {
				class:
					'kiyose-repeater-row__input kiyose-repeater-row__input--keyword-label kw-label',
				placeholder: strings.keywordLabelPlaceholder,
				type: 'text',
			}),
			$('<input>', {
				class:
					'kiyose-repeater-row__input kiyose-repeater-row__input--keyword-url kw-url',
				placeholder: strings.keywordUrlPlaceholder,
				type: 'text',
			}),
			$('<button>', {
				class: 'button kw-remove',
				text: strings.removeLabel,
				type: 'button',
			})
		);

	const createQAField = (label, field) =>
		$('<div>', { class: 'kiyose-repeater-row__field' }).append(
			$('<label>', {
				class: 'kiyose-repeater-row__label',
				text: label,
			}),
			field
		);

	const createQARow = () =>
		$('<div>', {
			class: 'kiyose-repeater-row kiyose-repeater-row--qa qa-row',
		}).append(
			createQAField(
				strings.qaQuestionLabel,
				$('<input>', {
					class: 'kiyose-repeater-row__input qa-question',
					type: 'text',
				})
			),
			createQAField(
				strings.qaAnswerLabel,
				$('<textarea>', {
					class: 'kiyose-repeater-row__textarea qa-answer',
					rows: 3,
				})
			),
			$('<button>', {
				class: 'button qa-remove',
				text: strings.removeLabel,
				type: 'button',
			})
		);

	const getAttachmentPreviewUrl = (attachment, preferredSize) => {
		if (
			attachment.sizes &&
			attachment.sizes[preferredSize] &&
			attachment.sizes[preferredSize].url
		) {
			return attachment.sizes[preferredSize].url;
		}

		return attachment.url || '';
	};

	const setupMediaField = ({
		buttonSelector,
		inputSelector,
		previewSelector,
		removeSelector,
		title,
		preferredSize,
		imageClass,
	}) => {
		$(document).on('click', buttonSelector, (event) => {
			event.preventDefault();

			if (!window.wp || !window.wp.media) {
				return;
			}

			if (mediaFrames[inputSelector]) {
				mediaFrames[inputSelector].open();
				return;
			}

			mediaFrames[inputSelector] = window.wp.media({
				button: {
					text: strings.mediaButtonText,
				},
				multiple: false,
				title,
			});

			mediaFrames[inputSelector].on('select', () => {
				const attachment = mediaFrames[inputSelector]
					.state()
					.get('selection')
					.first()
					.toJSON();
				const previewUrl = getAttachmentPreviewUrl(attachment, preferredSize);

				$(inputSelector).val(attachment.id || '');
				$(previewSelector)
					.empty()
					.append(
						$('<img>', {
							alt: '',
							class: imageClass,
							src: previewUrl,
						})
					);
				$(removeSelector).prop('hidden', false);
			});

			mediaFrames[inputSelector].open();
		});

		$(document).on('click', removeSelector, (event) => {
			event.preventDefault();

			$(inputSelector).val('');
			$(previewSelector).empty();
			$(event.currentTarget).prop('hidden', true);
		});
	};

	const syncTemplateFields = () => {
		const selectedTemplate = String($('#page_template').val() || '');

		$('.kiyose-meta-box[data-template-required]')
			.toArray()
			.forEach((box) => {
				const $box = $(box);
				const $notice = $box.find('.kiyose-meta-box__notice');

				if (!$notice.length) {
					return;
				}

				const isVisible =
					String($box.data('template-required') || '') === selectedTemplate;

				$box.find('.kiyose-meta-box__fields').prop('hidden', !isVisible);
				$notice.prop('hidden', isVisible);
			});
	};

	$(() => {
		$('#kiyose_keyword_add').on('click', () => {
			$('#kiyose_welcome_keywords_list').append(createKeywordRow());
		});

		$(document).on('click', '.kw-remove', (event) => {
			$(event.currentTarget).closest('.keyword-row').remove();
			serializeKeywords();
		});

		$(document).on('change input', '#kiyose_welcome_keywords_list input', () => {
			serializeKeywords();
		});

		$('#kiyose_qa_add').on('click', () => {
			$('#kiyose_content1_qa_list').append(createQARow());
		});

		$(document).on('click', '.qa-remove', (event) => {
			$(event.currentTarget).closest('.qa-row').remove();
			serializeQA();
		});

		$(document).on(
			'change input',
			'#kiyose_content1_qa_list input, #kiyose_content1_qa_list textarea',
			() => {
				serializeQA();
			}
		);

		$('form#post').on('submit', () => {
			serializeKeywords();
			serializeQA();
		});

		setupMediaField({
			buttonSelector: '#kiyose_hero_image_button',
			imageClass: 'kiyose-meta-box__image-preview-image',
			inputSelector: '#kiyose_hero_image_id',
			preferredSize: 'medium_large',
			previewSelector: '#kiyose_hero_image_preview',
			removeSelector: '#kiyose_hero_image_remove',
			title: strings.heroMediaTitle,
		});

		setupMediaField({
			buttonSelector: '#kiyose_contact_photo_button',
			imageClass:
				'kiyose-meta-box__image-preview-image kiyose-meta-box__image-preview-image--contact',
			inputSelector: '#kiyose_contact_photo_id',
			preferredSize: 'medium',
			previewSelector: '#kiyose_contact_photo_preview',
			removeSelector: '#kiyose_contact_photo_remove',
			title: strings.contactMediaTitle,
		});

		$('#page_template').on('change', syncTemplateFields);
		syncTemplateFields();
	});
})(jQuery);
