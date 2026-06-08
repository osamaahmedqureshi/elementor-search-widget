(function($){
  'use strict';

  function getPortals($box){ return $box.data('hjEswPortals') || $(); }

  function ensurePortal($box){
    if(!($box.hasClass('hj-esw-layout-fullscreen') || $box.hasClass('hj-esw-layout-halfscreen'))) return;
    if($box.data('hjEswPortaled')) return;
    var $items = $box.children('.hj-esw-overlay, .hj-esw-panel-backdrop, .hj-esw-panel');
    $items.each(function(){
      var $item = $(this);
      $item.data('hjEswOwner', $box);
      $('body').append($item);
    });
    $box.data('hjEswPortals', $items);
    $box.data('hjEswPortaled', true);
  }

  function closeAllExcept($keep){
    $('.hj-esw.hj-esw-open').not($keep).each(function(){ closeBox($(this)); });
  }

  function openBox($box){
    ensurePortal($box);
    closeAllExcept($box);
    $box.addClass('hj-esw-open');
    $box.find('> .hj-esw-trigger').attr('aria-expanded','true');
    if($box.hasClass('hj-esw-layout-fullscreen') || $box.hasClass('hj-esw-layout-halfscreen')){
      $('body').addClass('hj-esw-body-lock');
      getPortals($box).addClass('hj-esw-portal-open').attr('aria-hidden','false');
    }
    setTimeout(function(){
      var $input = getPortals($box).find('.hj-esw-input:visible').first();
      if(!$input.length) $input = $box.find('.hj-esw-input:visible').first();
      $input.trigger('focus');
    }, 90);
  }

  function closeBox($box){
    $box.removeClass('hj-esw-open');
    $box.find('> .hj-esw-trigger').attr('aria-expanded','false');
    getPortals($box).removeClass('hj-esw-portal-open').attr('aria-hidden','true');
    $box.find('.hj-esw-results').prop('hidden', true).empty();
    getPortals($box).find('.hj-esw-results').prop('hidden', true).empty();
    if(!$('.hj-esw-open.hj-esw-layout-fullscreen,.hj-esw-open.hj-esw-layout-halfscreen').length){
      $('body').removeClass('hj-esw-body-lock');
    }
  }

  function bindAjax($context, $box){
    $context.find('.hj-esw-input').each(function(){
      var $input=$(this), $form=$input.closest('.hj-esw-form'), $results=$form.find('.hj-esw-results').first(), timer=null, last='';
      function hide(){ $results.prop('hidden', true).empty(); }
      function render(items){
        $results.empty();
        if(!items || !items.length){
          $results.append('<div class="hj-esw-result-empty">No results found</div>');
        }else{
          $.each(items,function(i,item){ $('<a/>',{class:'hj-esw-result-item',href:item.url,text:item.title}).appendTo($results); });
        }
        $results.prop('hidden', false);
      }
      $input.off('input.hjEsw').on('input.hjEsw', function(){
        if($box.data('ajax') !== 'yes') return;
        var keyword=$.trim($input.val());
        clearTimeout(timer);
        if(keyword.length < 2){ hide(); return; }
        timer=setTimeout(function(){
          if(keyword === last) return;
          last=keyword;
          $form.find('.hj-esw-button').addClass('hj-esw-loading');
          $.post(HJ_ESW.ajaxUrl, {
            action:'hj_esw_search', nonce:HJ_ESW.nonce, keyword:keyword,
            source:$box.data('source') || 'posts', limit:$box.data('limit') || 5
          }).done(function(res){ render(res && res.success ? res.data : []); })
            .fail(function(){ hide(); })
            .always(function(){ $form.find('.hj-esw-button').removeClass('hj-esw-loading'); });
        }, 260);
      });
      $input.off('keydown.hjEsw').on('keydown.hjEsw', function(e){
        if(e.key === 'Escape'){ closeBox($box); }
      });
    });
  }

  function initSearch($scope){
    $scope.find('.hj-esw').each(function(){
      var $box=$(this);
      if($box.data('hj-esw-ready')) return;
      $box.data('hj-esw-ready', true);
      ensurePortal($box);
      bindAjax($box, $box);
      bindAjax(getPortals($box), $box);
      $box.find('> .hj-esw-trigger').on('click.hjEsw', function(e){
        e.preventDefault();
        $box.hasClass('hj-esw-open') ? closeBox($box) : openBox($box);
      });
      getPortals($box).filter('.hj-esw-panel-backdrop').add(getPortals($box).find('.hj-esw-close')).on('click.hjEsw', function(e){
        e.preventDefault(); e.stopPropagation(); closeBox($box);
      });
      if($box.hasClass('hj-esw-layout-creative')){
        $box.find('.hj-esw-button').on('click.hjEswCreative', function(e){
          var val=$.trim($box.find('.hj-esw-input').val());
          if(!$box.hasClass('hj-esw-open')){ e.preventDefault(); openBox($box); return false; }
          if(!val){ e.preventDefault(); $box.find('.hj-esw-input').trigger('focus'); return false; }
        });
      }
    });
  }

  $(document).on('click.hjEswClose','body > .hj-esw-overlay .hj-esw-close, body > .hj-esw-panel .hj-esw-close, body > .hj-esw-panel-backdrop',function(e){
    e.preventDefault(); e.stopPropagation();
    var $owner = $(this).closest('.hj-esw-overlay, .hj-esw-panel, .hj-esw-panel-backdrop').data('hjEswOwner');
    if($owner && $owner.length) closeBox($owner);
  });

  $(document).on('keydown.hjEswGlobal', function(e){
    if(e.key === 'Escape') $('.hj-esw.hj-esw-open').each(function(){ closeBox($(this)); });
  });

  $(document).on('click.hjEswGlobal', function(e){
    $('.hj-esw.hj-esw-open').each(function(){
      var $box=$(this);
      if($box.hasClass('hj-esw-layout-fullscreen') || $box.hasClass('hj-esw-layout-halfscreen')) return;
      if(!$(e.target).closest($box).length) closeBox($box);
    });
    $('.hj-esw-results').each(function(){
      if(!$(e.target).closest($(this).closest('.hj-esw-form')).length) $(this).prop('hidden', true).empty();
    });
  });

  $(window).on('elementor/frontend/init', function(){
    elementorFrontend.hooks.addAction('frontend/element_ready/hj-search.default', initSearch);
  });
  $(function(){ initSearch($(document)); });
})(jQuery);
