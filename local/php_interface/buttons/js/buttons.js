
const Buttons = BX.namespace('Buttons');

const LeftColumn = 'intranet-user-profile-column-left';
const BlockColumn = 'intranet-user-profile-column-block';
const BlockColumnTitle = 'intranet-user-profile-column-block-title';
const BlockColumnTitleSpan = 'intranet-user-profile-column-block-title-text';

/**
 * Формирование области с кнопками
 * @param {integer} userId
 */

Buttons.createArea = function(userId) {
    
    const BlockColumnArea = BX.create('div', {
        attrs: {
            className: BlockColumn
        }
    })

    const Sidebar = this.getSidebar();
    const SidebarBlock = Sidebar.appendChild(BlockColumnArea);
    SidebarBlock.appendChild(BX.create('div', {
        attrs: {
            className: BlockColumnTitle
        },
        children: [
            BX.create('span', {
                attrs: {
                    className: BlockColumnTitleSpan
                },
                text: BX.message('BUTTONS_TITLE')
            })
        ]
    }))

    const ButtonCrmParams = {
        userId: userId,
        url: BX.message('BUTTONS_URL'),
        pageTitle: BX.message('BUTTONS_DEALS'),
        buttonText: BX.message('BUTTONS_DEALS'),
        cssClass: BX.message('BUTTONS_DEALS_CLASS'),
        templateName: BX.message('BUTTONS_DEALS_TEMPLATE_NAME')
        
    };

    const ButtonTelephonyParams = {
        userId: userId,
        url: BX.message('BUTTONS_URL'),
        pageTitle: BX.message('BUTTONS_CALL_HISTORY'),
        buttonText: BX.message('BUTTONS_CALL_HISTORY'),
        cssClass: BX.message('BUTTONS_CALL_CLASS'),
        templateName: BX.message('BUTTONS_CALL_TEMPLATE_NAME')
    };

    let arButtons = [];

    arButtons.push(this.createButton(ButtonCrmParams));
    arButtons.push(this.createButton(ButtonTelephonyParams));

    arButtons.forEach(button => {
        SidebarBlock.appendChild(BX.create('div', {
            style: {
                margin: '1em 0'
            },
            children: [button]
        }))
    })

}

/**
 * Поиск места для встройки кнопок
 */

Buttons.getSidebar = function() {
    return BX.findChildByClassName(document, LeftColumn, true);
}



/**
 * Формирование объекта кнопки
 * 
 * @param {object} params
 * @example params = {
 *      userId: userId,
 *      pageTitle: pageTitle,
 *      cssClass: cssClass,
 *      templateName: templateName,
 *      url: url,
 *      buttonText: buttonText
 * }
 * 
 * @return {object}
 */

 Buttons.createButton = function(params) {
    return BX.create('button', {
        attrs: {
            className: params.cssClass
        },
        text: params.buttonText,
        events: {
            click: () => {
                BX.SidePanel.Instance.open(params.url, {
                    cacheable: false,
                    allowChangeHistory: false,
                    allowChangeTitle: true,
                    Title: params.pageTitle,
                    requestMethod: 'POST',
                    requestParams: {
                        user: params.userId,
                        template: params.templateName,
                        title: params.pageTitle
                    },
                    label: {
                        text: params.pageTitle,
                        color: '#FFFFFF',
                        bgColor: '#2FC6F6',
                        opacity: 100
                    }
                })
            }
        }

     });
 }