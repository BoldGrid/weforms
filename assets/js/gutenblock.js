/**
 * weForms Block
 *
 * A block for embedding a weForms form into a post/page.
 */
( function( blocks, i18n, editor, element, components ) {

    var el = element.createElement, // function to create elements
        TextControl = components.TextControl,// text input control
        InspectorControls = editor.InspectorControls; // sidebar controls

    // register our block
    blocks.registerBlockType( 'weforms/form', {
        title: 'weForms',
        icon: 'feedback',
        category: 'common',

        attributes: {
            formID: {
                type: 'integer',
                default: 0
            },
            formName: {
                type: 'string',
                default: ''
            }
        },

        edit: function( props ) {

            var formID = props.attributes.formID;

            var formName = props.attributes.formName;

            var children = [];

            if( ! formID ) formID = ''; // Default.
            if( ! formName ) formName = ''; // Default

            // this function is required, but we don't need it to do anything
            function weFormsOnValueChange( formName ) { }

            // show the dropdown when we click on the input
            function weFormsFocusClick( event ) {
                var elID = event.target.getAttribute( 'id' );
                var idArray = elID.split( '-' );
                var weFormsOptions = document.getElementById( 'weforms-filter-container-' + idArray[ idArray.length -1 ] );
                // get the related input element
                var weFormsInput = document.getElementById( 'weforms-formFilter-' + idArray[ idArray.length -1 ] );
                // set focus to the element so the onBlur function runs properly
                weFormsInput.focus();
                weFormsOptions.style.display = 'block';
            }

            // function for select the form on filter drop down item click
            function selectForm( event ) {
                //set the attributes from the selected for item
                props.setAttributes( {
                    formID: parseInt( event.target.getAttribute( 'data-formid' ) ),
                    formName: event.target.innerText
                } );
                /**
                 * Get the main div of the filter to tell if this is being
                 * selected from the sidebar or block so we can hide the dropdown
                 */
                var elID = event.target.parentNode.parentNode;
                var idArray = elID.getAttribute( 'id' ).split( '-' );
                var weFormsOptions = document.getElementById( 'weforms-filter-container-' + idArray[ idArray.length -1 ] );
                var inputEl = document.getElementById( 'weforms-formFilter-sidebar' );

                if( inputEl ) {
                    inputEl.value = '';
                }
                weFormsOptions.style.display = 'none';
            }

            function weFormsHideOptions( event ) {
                /**
                 * Get the main div of the filter to tell if this is being
                 * selected from the sidebar or block so we can hide the dropdown
                 */
                var elID = event.target.getAttribute( 'id' );
                var idArray = elID.split( '-' );
                var weFormsOptions = document.getElementById( 'weforms-filter-container-' + idArray[ idArray.length -1 ] );
                weFormsOptions.style.display = 'none';
            }

            function weFormsInputKeyUp( event ) {
                var val = event.target.value;
                /**
                 * Get the main div of the filter to tell if this is being
                 * selected from the sidebar or block so we can SHOW the dropdown
                 */
                var filterInputContainer = event.target.parentNode.parentNode.parentNode;
                filterInputContainer.querySelector( '.weforms-filter-option-container' ).style.display = 'block';
                filterInputContainer.style.display = 'block';

                // Let's filter the forms here
                _.each( weFormsBlock.forms, function( form, index ) {
                    var liEl = filterInputContainer.querySelector( "[data-formid='" + form.value + "']" );
                    if ( 0 <= form.label.toLowerCase().indexOf( val.toLowerCase() ) ) {
                        // shows options that DO contain the text entered
                        liEl.style.display = 'block';
                    } else {
                        // hides options the do not contain the text entered
                        liEl.style.display = 'none';
                    }
                });
            }

            // Set up the form items from the localized php variables
            var formItems = [];
            _.each( weFormsBlock.forms, function( form, index ) {
                formItems.push( el( 'li', { className: 'weforms-filter-option',
                        'data-formid': form.value, onMouseDown: selectForm},
                        form.label + " ( ID: " + form.value + " )" ))
            });

            // Set up form filter for the block
            var inputFilterMain = el( 'div', { id: 'weforms-filter-input-main',
                    className: 'weforms-filter-input' },
                el( TextControl, { id: 'weforms-formFilter-main',
                    placeHolder: 'Select a Form',
                    className: 'weforms-filter-input-el blocks-select-control__input',
                    onChange: weFormsOnValueChange,
                    onClick: weFormsFocusClick,
                    onKeyUp: weFormsInputKeyUp,
                    onBlur: weFormsHideOptions
                } ),
                el( 'span', { id: 'weforms-filter-input-icon-main',
                    className: 'weforms-filter-input-icon',
                    onClick: weFormsFocusClick,
                    dangerouslySetInnerHTML: { __html: '&#9662;' } } ),
                el( 'div', { id: 'weforms-filter-container-main',
                        className: 'weforms-filter-option-container' },
                        el( 'ul', null, formItems )
                )
            );
            // Create filter input for the sidebar blocks settings
            var inputFilterSidebar = el( 'div', { id: 'weforms-filter-input-sidebar',
                    className: 'weforms-filter-input' },
                el( TextControl, { id: 'weforms-formFilter-sidebar',
                    placeHolder: 'Select a Form',
                    className: 'weforms-filter-input-el blocks-select-control__input',
                    onChange: weFormsOnValueChange,
                    onClick: weFormsFocusClick,
                    onKeyUp: weFormsInputKeyUp,
                    onBlur: weFormsHideOptions
                } ),
                el( 'span', { id: 'weforms-filter-input-icon-sidebar',
                    className: 'weforms-filter-input-icon',
                    onClick: weFormsFocusClick,
                    dangerouslySetInnerHTML: { __html: '&#9662;' } } ),
                el( 'div', { id: 'weforms-filter-container-sidebar',
                        className: 'weforms-filter-option-container' },
                    el( 'ul', null, formItems )
                )
            );

            // Set up the form filter dropdown in the side bar 'block' settings
            var inspectorControls = el( InspectorControls, {},
                el( 'span', null, 'Current selected form:' ),
                el( 'br', null ),
                el( 'span', null, formName ),
                el( 'br', null ),
                el ('hr', null ),
                el ( 'label', { for: 'weforms-formFilter-sidebar' }, 'Type to' +
                    ' filter' +
                    ' forms' ),
                inputFilterSidebar
            );

            /**
             * Create the div container, add an overlay so the user can interact
             * with the form in Gutenberg, then render the iframe with form
             */
             if ( '' === formID ) {
                children.push( el( 'div', {style : {width: '100%'}},
                    el( 'img', { className: 'weforms-block-logo', src: weFormsBlock.block_logo}),
                    el ( 'div', null, 'weForms Forms'),
                    inputFilterMain
                ) );
            } else {
                children.push(
                    el( 'div', { className: 'weforms-iframe-container' },
                        el( 'div', { className: 'weforms-iframe-overlay' } ),
                        el( 'iframe', { src: weFormsBlock.siteUrl + '/?weforms_preview=1&weforms_iframe&form_id=' + formID, height: '0', width: '500', scrolling: 'no' })
                    )
                )
            }
            children.push(inspectorControls);
            return [
                children
            ];
        },

        save: function( props ) {
            var formID = props.attributes.formID;

            if( ! formID ) return '';
            /**
             * we're essentially just adding a short code, here is where
             * it's save in the editor
             *
             * return content wrapped in DIV b/c raw HTML is unsupported
             * going forward
             */
            var returnHTML = '[weforms id=' + parseInt( formID ) + ']';
            return el( 'div', null, returnHTML );
        }
    } );


} )(
    window.wp.blocks,
    window.wp.i18n,
    window.wp.editor,
    window.wp.element,
    window.wp.components
);