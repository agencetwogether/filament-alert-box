@php
    use Filament\Support\Icons\Heroicon;
    use Illuminate\Support\Arr;
    use Illuminate\View\ComponentAttributeBag;
    use function Filament\Support\generate_icon_html;
    use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;

    $color = 'alert-box-'.$type->value;
    $styles = Arr::toCssStyles([
        Filament\Support\get_color_css_variables($color, shades: [100, 400, 500, 950]) => $color !== 'alert-box-none',
    ]);

@endphp
<div @class([
    'flex gap-1 items-center px-2 py-1 rounded-md text-sm font-bold',
    'bg-custom-400/10 text-custom-950 dark:bg-custom-500/20 dark:text-custom-100' => $type->value != 'none',
    'bg-white text-black dark:bg-gray-500/20 dark:text-gray-100' => $type->value == 'none',
]) style="{{ $styles }}">

    @if(filled($type->getIcon()))
        {{
            generate_icon_html($type->getIcon(), null, (new ComponentAttributeBag)
                    ->color(IconComponent::class, $color))
        }}
    @endif <span>{{ $type->getLabel() }}</span>
</div>
