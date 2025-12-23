@foreach($sections as $section)
    @if($section['enabled'] ?? true)
        @php
            $sectionObj = (object) $section;
            $sectionObj->settings = $section['settings'] ?? [];
            $sectionObj->title = $section['title'] ?? '';
            $sectionObj->content = $section['content'] ?? '';
            
            // Responsive Styles - Padding
            $pTop = $section['settings']['padding_top'] ?? null;
            $pBottom = $section['settings']['padding_bottom'] ?? null;
            $pLeft = $section['settings']['padding_left'] ?? null;
            $pRight = $section['settings']['padding_right'] ?? null;
            $pTopMob = $section['settings']['padding_top_mobile'] ?? null; 
            $pBottomMob = $section['settings']['padding_bottom_mobile'] ?? null;
            $pLeftMob = $section['settings']['padding_left_mobile'] ?? null;
            $pRightMob = $section['settings']['padding_right_mobile'] ?? null;
            
            // Responsive Styles - Margin
            $mTop = $section['settings']['margin_top'] ?? null;
            $mBottom = $section['settings']['margin_bottom'] ?? null;
            $mLeft = $section['settings']['margin_left'] ?? null;
            $mRight = $section['settings']['margin_right'] ?? null;
            $mTopMob = $section['settings']['margin_top_mobile'] ?? null;
            $mBottomMob = $section['settings']['margin_bottom_mobile'] ?? null;
            $mLeftMob = $section['settings']['margin_left_mobile'] ?? null;
            $mRightMob = $section['settings']['margin_right_mobile'] ?? null;
            
            // Typography Styles
            $textAlign = $section['settings']['text_align'] ?? null;
            $textAlignMob = $section['settings']['text_align_mobile'] ?? null;
            $titleFontSize = $section['settings']['title_font_size'] ?? null;
            $titleFontSizeMob = $section['settings']['title_font_size_mobile'] ?? null;
            $contentFontSize = $section['settings']['content_font_size'] ?? null;
            $contentFontSizeMob = $section['settings']['content_font_size_mobile'] ?? null;
            $fontWeight = $section['settings']['font_weight'] ?? null;
            $titleColor = $section['settings']['title_color'] ?? null;
            $contentColor = $section['settings']['content_color'] ?? null;
            $hideOnDesktop = $section['settings']['hide_on_desktop'] ?? false;
            $hideOnMobile = $section['settings']['hide_on_mobile'] ?? false;

            // Ensure units
            if(is_numeric($pTop)) $pTop .= 'px';
            if(is_numeric($pBottom)) $pBottom .= 'px';
            if(is_numeric($pLeft)) $pLeft .= 'px';
            if(is_numeric($pRight)) $pRight .= 'px';
            if(is_numeric($pTopMob)) $pTopMob .= 'px';
            if(is_numeric($pBottomMob)) $pBottomMob .= 'px';
            if(is_numeric($pLeftMob)) $pLeftMob .= 'px';
            if(is_numeric($pRightMob)) $pRightMob .= 'px';
            
            if(is_numeric($mTop)) $mTop .= 'px';
            if(is_numeric($mBottom)) $mBottom .= 'px';
            if(is_numeric($mLeft)) $mLeft .= 'px';
            if(is_numeric($mRight)) $mRight .= 'px';
            if(is_numeric($mTopMob)) $mTopMob .= 'px';
            if(is_numeric($mBottomMob)) $mBottomMob .= 'px';
            if(is_numeric($mLeftMob)) $mLeftMob .= 'px';
            if(is_numeric($mRightMob)) $mRightMob .= 'px';
            
            if(is_numeric($titleFontSize)) $titleFontSize .= 'px';
            if(is_numeric($titleFontSizeMob)) $titleFontSizeMob .= 'px';
            if(is_numeric($contentFontSize)) $contentFontSize .= 'px';
            if(is_numeric($contentFontSizeMob)) $contentFontSizeMob .= 'px';
            
            $uID = 'pb-sec-preview-' . $loop->index . '-' . uniqid();
        @endphp

        <style>
             #{{ $uID }} {
                @if($pTopMob) padding-top: {{ $pTopMob }} !important; @endif
                @if($pBottomMob) padding-bottom: {{ $pBottomMob }} !important; @endif
                @if($pLeftMob) padding-left: {{ $pLeftMob }} !important; @endif
                @if($pRightMob) padding-right: {{ $pRightMob }} !important; @endif
                @if($mTopMob) margin-top: {{ $mTopMob }} !important; @endif
                @if($mBottomMob) margin-bottom: {{ $mBottomMob }} !important; @endif
                @if($mLeftMob) margin-left: {{ $mLeftMob }} !important; @endif
                @if($mRightMob) margin-right: {{ $mRightMob }} !important; @endif
                @if($textAlignMob) text-align: {{ $textAlignMob }} !important; @endif
                @if($fontWeight) font-weight: {{ $fontWeight }} !important; @endif
                @if($hideOnMobile) display: none !important; @endif
            }
            @if($titleFontSizeMob || $titleColor)
            #{{ $uID }} h1, #{{ $uID }} h2, #{{ $uID }} h3, #{{ $uID }} .section-title {
                @if($titleFontSizeMob) font-size: {{ $titleFontSizeMob }} !important; @endif
                @if($titleColor) color: {{ $titleColor }} !important; @endif
            }
            @endif
            @if($contentFontSizeMob || $contentColor)
            #{{ $uID }} p, #{{ $uID }} .section-content {
                @if($contentFontSizeMob) font-size: {{ $contentFontSizeMob }} !important; @endif
                @if($contentColor) color: {{ $contentColor }} !important; @endif
            }
            @endif
            @media (min-width: 768px) {
                #{{ $uID }} {
                    @if($pTop) padding-top: {{ $pTop }} !important; @endif
                    @if($pBottom) padding-bottom: {{ $pBottom }} !important; @endif
                    @if($pLeft) padding-left: {{ $pLeft }} !important; @endif
                    @if($pRight) padding-right: {{ $pRight }} !important; @endif
                    @if($mTop) margin-top: {{ $mTop }} !important; @endif
                    @if($mBottom) margin-bottom: {{ $mBottom }} !important; @endif
                    @if($mLeft) margin-left: {{ $mLeft }} !important; @endif
                    @if($mRight) margin-right: {{ $mRight }} !important; @endif
                    @if($textAlign) text-align: {{ $textAlign }} !important; @endif
                    @if($hideOnDesktop) display: none !important; @endif
                }
                @if($titleFontSize)
                #{{ $uID }} h1, #{{ $uID }} h2, #{{ $uID }} h3, #{{ $uID }} .section-title {
                    font-size: {{ $titleFontSize }} !important;
                }
                @endif
                @if($contentFontSize)
                #{{ $uID }} p, #{{ $uID }} .section-content {
                    font-size: {{ $contentFontSize }} !important;
                }
                @endif
            }
        </style>
        
        <div data-pb-index="{{ $loop->index }}" class="pb-section-wrapper relative group">
             @includeIf('storefront.sections.' . $section['type'], ['section' => $sectionObj, 'section_id' => $uID])
        </div>
    @endif
@endforeach
