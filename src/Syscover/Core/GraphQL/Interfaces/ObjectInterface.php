<?php namespace Syscover\Core\GraphQL\Interfaces;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\InterfaceType;
use Syscover\Core\GraphQL\Types\AnyType;
use Syscover\Admin\Models\User;
use Syscover\Admin\Models\Action;
use Syscover\Admin\Models\AttachmentFamily;
use Syscover\Admin\Models\AttachmentMime;
use Syscover\Admin\Models\Country;
use Syscover\Admin\Models\Field;
use Syscover\Admin\Models\FieldGroup;
use Syscover\Admin\Models\FieldValue;
use Syscover\Admin\Models\Lang;
use Syscover\Admin\Models\Package;
use Syscover\Admin\Models\Profile;
use Syscover\Admin\Models\Resource;
use Syscover\Cms\Models\Article;
use Syscover\Cms\Models\Category;
use Syscover\Cms\Models\Family;
use Syscover\Cms\Models\Section;
use Syscover\Crm\Models\Group;
use Syscover\Market\Models\CustomerClassTax;
use Syscover\Market\Models\OrderStatus;
use Syscover\Market\Models\PaymentMethod;
use Syscover\Market\Models\ProductClassTax;

class ObjectInterface extends InterfaceType
{
    protected $attributes = [
        'name'          => 'ObjectInterface',
        'description'   => 'Interface to define any element that is in database'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(app(AnyType::class)),
                'description' => 'The id of action'
            ]
        ];
    }

    public function resolveType($object)
    {
        switch (get_class($object))
        {
            // ADMIN
            case Package::class:
                return GraphQL::type('AdminPackage');
                break;

            case Country::class:
                return GraphQL::type('AdminCountry');
                break;

            case Lang::class:
                return GraphQL::type('AdminLang');
                break;

            case Action::class:
                return GraphQL::type('AdminAction');
                break;

            case Resource::class:
                return GraphQL::type('AdminResource');
                break;

            case Profile::class:
                return GraphQL::type('AdminProfile');
                break;

            case AttachmentFamily::class:
                return GraphQL::type('AdminAttachmentFamily');
                break;

            case AttachmentMime::class:
                return GraphQL::type('AdminAttachmentMime');
                break;

            case FieldGroup::class:
                return GraphQL::type('AdminFieldGroup');
                break;

            case Field::class:
                return GraphQL::type('AdminField');
                break;

            case FieldValue::class:
                return GraphQL::type('AdminFieldValue');
                break;

            case User::class:
                return GraphQL::type('AdminUser');
                break;

            // CMS
            case Section::class:
                return GraphQL::type('CmsSection');
                break;

            case Family::class:
                return GraphQL::type('CmsFamily');
                break;

            case Category::class:
                return GraphQL::type('CmsCategory');
                break;

            case Article::class:
                return GraphQL::type('CmsArticle');
                break;

            // CRM
            case Group::class:
                return GraphQL::type('CrmGroup');
                break;

            // MARKET
            case OrderStatus::class:
                return GraphQL::type('MarketOrderStatus');
                break;

            case PaymentMethod::class:
                return GraphQL::type('MarketPaymentMethod');
                break;

            case CustomerClassTax::class:
                return GraphQL::type('MarketCustomerClassTax');
                break;

            case ProductClassTax::class:
                return GraphQL::type('MarketProductClassTax');
                break;
        }
    }
}