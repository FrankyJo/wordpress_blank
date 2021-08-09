<?php

/**
 * Хлебные крошки для WordPress (breadcrumbs)
 *
 * @param string [$sep  = '']      Разделитель. По умолчанию ' » '
 * @param array  [$l10n = array()] Для локализации. См. переменную $default_l10n.
 * @param array  [$args = array()] Опции. См. переменную $def_args
 * @return string Выводит на экран HTML код
 *
 * version 3.3.2
 */
function phonexa_breadcrumbs($sep = ' > ', $l10n = array(), $args = array())
{
    $pb = new Phonexa_Breadcrumbs;
    echo $pb->get_crumbs($sep, $l10n, $args);
}

class Phonexa_Breadcrumbs
{

    public $arg;

    // Локализация
    static $l10n = array(
        'home' => 'Phonexa',
        'paged' => 'Page %d',
        '_404' => 'Error 404',
        'search' => 'Search',
        'author' => '<li><a href="/blog/"><span>Blog</span></a><li> %s ',
        'year' => 'Archive <b>%d</b> год',
        'month' => 'Archive: <b>%s</b>',
        'day' => '',
        'attachment' => 'Media: %s',
        'tag' => 'Post by tag: <b>%s</b>',
        'tax_tag' => '%1$s in "%2$s" by tag: <b>%3$s</b>',
        // tax_tag выведет: 'тип_записи из "название_таксы" по тегу: имя_термина'.
        // Если нужны отдельные холдеры, например только имя термина, пишем так: 'записи по тегу: %3$s'
    );

    // Параметры по умолчанию
    static $args = array(
        'on_front_page' => true,  // выводить крошки на главной странице
        'show_post_title' => true,  // показывать ли название записи в конце (последний элемент). Для записей, страниц, вложений
        'show_term_title' => true,  // показывать ли название элемента таксономии в конце (последний элемент). Для меток, рубрик и других такс
        'title_patt' => '<li class="kb_title">%s</li>', // шаблон для последнего заголовка. Если включено: show_post_title или show_term_title
        'last_sep' => true,  // показывать последний разделитель, когда заголовок в конце не отображается
        'markup' => 'schema.org', // 'markup' - микроразметка. Может быть: 'rdf.data-vocabulary.org', 'schema.org', '' - без микроразметки
        // или можно указать свой массив разметки:
        // array( 'wrappatt'=>'<div class="phonexa_breadcrumbs">%s</div>', 'linkpatt'=>'<a href="%s">%s</a>', 'sep_after'=>'', )
        'priority_tax' => array('category'), // приоритетные таксономии, нужно когда запись в нескольких таксах
        'priority_terms' => array(), // 'priority_terms' - приоритетные элементы таксономий, когда запись находится в нескольких элементах одной таксы одновременно.
        // Например: array( 'category'=>array(45,'term_name'), 'tax_name'=>array(1,2,'name') )
        // 'category' - такса для которой указываются приор. элементы: 45 - ID термина и 'term_name' - ярлык.
        // порядок 45 и 'term_name' имеет значение: чем раньше тем важнее. Все указанные термины важнее неуказанных...
        'nofollow' => false, // добавлять rel=nofollow к ссылкам?

        // служебные
        'sep' => '',
        'linkpatt' => '',
        'pg_end' => '',

    );

    function get_crumbs($sep, $l10n, $args)
    {
        global $post, $wp_query, $wp_post_types;

        self::$args['sep'] = $sep;

        // Фильтрует дефолты и сливает
        $loc = (object)array_merge(apply_filters('phonexa_breadcrumbs_default_loc', self::$l10n), $l10n);
        $arg = (object)array_merge(apply_filters('phonexa_breadcrumbs_default_args', self::$args), $args);

        $arg->sep = ''; // дополним ( пример сепаратора: <span class="kb_sep">'. $arg->sep .'</span>)

        // упростим
        $sep = &$arg->sep;
        $this->arg = &$arg;

        // микроразметка ---
        if (1) {
            $mark = &$arg->markup;

            // Разметка по умолчанию
            if (!$mark) $mark = array(
                'wrappatt' => '<div class="phonexa_breadcrumbs ">%s</div>',
                'linkpatt' => '<a href="%s">%s</a>',
                'sep_after' => '',
            );
            // rdf
            elseif ($mark === 'rdf.data-vocabulary.org') $mark = array(
                'wrappatt' => '<div class="phonexa_breadcrumbs" prefix="v: http://rdf.data-vocabulary.org/#">%s</div>',
                'linkpatt' => '<span typeof="v:Breadcrumb"><a href="%s" rel="v:url" property="v:title">%s</a>',
                'sep_after' => '</span>', // закрываем span после разделителя!
            );
            // schema.org
            elseif ($mark === 'schema.org') $mark = array(
                'wrappatt' => '<ul class="phonexa_breadcrumbs breadcrumbs ">%s</ul>',
                'linkpatt' => '<li><a href="%s"><span>%s</span></a></li>',
                'sep_after' => '',
            );

            elseif (!is_array($mark))
                die(__CLASS__ . ': "markup" parameter must be array...');

            $wrappatt = $mark['wrappatt'];
            $arg->linkpatt = $arg->nofollow ? str_replace('<a ', '<a rel="nofollow"', $mark['linkpatt']) : $mark['linkpatt'];
            $arg->sep .= $mark['sep_after'] . "\n";


        }

        $linkpatt = $arg->linkpatt; // упростим

        $q_obj = get_queried_object();

        // может это архив пустой таксы?
        $ptype = null;
        if (empty($post)) {
            if (isset($q_obj->taxonomy))
                $ptype = &$wp_post_types[get_taxonomy($q_obj->taxonomy)->object_type[0]];
        } else $ptype = &$wp_post_types[$post->post_type];

        // paged
        $arg->pg_end = '';
        if (($paged_num = get_query_var('paged')) || ($paged_num = get_query_var('page')))
            $arg->pg_end = $sep . sprintf($loc->paged, (int)$paged_num);

        $pg_end = $arg->pg_end; // упростим

        $out = '';

        if (is_front_page()) {
            return $arg->on_front_page ? sprintf($wrappatt, ($paged_num ? sprintf($linkpatt, get_home_url(), $loc->home) . $pg_end : $loc->home)) : '';
        } // страница записей, когда для главной установлена отдельная страница.
        elseif (is_home()) {
            $out = $paged_num ? (sprintf($linkpatt, get_permalink($q_obj), esc_html($q_obj->post_title)) . $pg_end) : esc_html($q_obj->post_title);
        } elseif (is_404()) {
            $out = $loc->_404;
        } elseif (is_search()) {
            $out = sprintf($loc->search, esc_html($GLOBALS['s']));
        } elseif (is_author()) {
            $out = sprintf($loc->author, $q_obj->display_name);
        } elseif (is_year() || is_month() || is_day()) {
            $y_url = get_year_link($year = get_the_time('Y'));

            if (is_year()) {
                $tit = sprintf($loc->year, $year);
                $out = ($paged_num ? sprintf($linkpatt, $y_url, $tit) . $pg_end : $tit);
            } // month day
            else {
                $y_link = sprintf($linkpatt, $y_url, $year);
                $m_url = get_month_link($year, get_the_time('m'));

                if (is_month()) {
                    $tit = sprintf($loc->month, get_the_time('F'));
                    $out = $y_link . $sep . ($paged_num ? sprintf($linkpatt, $m_url, $tit) . $pg_end : $tit);
                } elseif (is_day()) {
                    $m_link = sprintf($linkpatt, $m_url, get_the_time('F'));
                    $out = $y_link . $sep . $m_link . $sep . get_the_time('l');
                }
            }
        } // Древовидные записи
        elseif (is_singular() && $ptype->hierarchical) {
            $out = $this->_add_title($this->_page_crumbs($post), $post);
        } // Таксы, плоские записи и вложения
        else {
            $term = $q_obj; // таксономии

            // определяем термин для записей (включая вложения attachments)
            if (is_singular()) {
                // изменим $post, чтобы определить термин родителя вложения
                if (is_attachment() && $post->post_parent) {
                    $save_post = $post; // сохраним
                    $post = get_post($post->post_parent);
                }

                // учитывает если вложения прикрепляются к таксам древовидным - все бывает :)
                $taxonomies = get_object_taxonomies($post->post_type);
                // оставим только древовидные и публичные, мало ли...
                $taxonomies = array_intersect($taxonomies, get_taxonomies(array('hierarchical' => true, 'public' => true)));

                if ($taxonomies) {
                    // сортируем по приоритету
                    if (!empty($arg->priority_tax)) {
                        usort($taxonomies, function ($a, $b) use ($arg) {
                            $a_index = array_search($a, $arg->priority_tax);
                            if ($a_index === false) $a_index = 9999999;

                            $b_index = array_search($b, $arg->priority_tax);
                            if ($b_index === false) $b_index = 9999999;

                            return ($b_index === $a_index) ? 0 : ($b_index < $a_index ? 1 : -1); // меньше индекс - выше
                        });
                    }

                    // пробуем получить термины, в порядке приоритета такс
                    foreach ($taxonomies as $taxname) {
                        if ($terms = get_the_terms($post->ID, $taxname)) {
                            // проверим приоритетные термины для таксы
                            $prior_terms = &$arg->priority_terms[$taxname];
                            if ($prior_terms && count($terms) > 2) {
                                foreach ((array)$prior_terms as $term_id) {
                                    $filter_field = is_numeric($term_id) ? 'term_id' : 'slug';
                                    $_terms = wp_list_filter($terms, array($filter_field => $term_id));

                                    if ($_terms) {
                                        $term = array_shift($_terms);
                                        break;
                                    }
                                }
                            } else
                                $term = array_shift($terms);

                            break;
                        }
                    }
                }

                if (isset($save_post)) $post = $save_post; // вернем обратно (для вложений)
            }

            // вывод

            // все виды записей с терминами или термины
            if ($term && isset($term->term_id)) {
                $term = apply_filters('phonexa_breadcrumbs_term', $term);

                // attachment
                if (is_attachment()) {
                    if (!$post->post_parent)
                        $out = sprintf($loc->attachment, esc_html($post->post_title));
                    else {
                        if (!$out = apply_filters('attachment_tax_crumbs', '', $term, $this)) {
                            $_crumbs = $this->_tax_crumbs($term, 'self');
                            $parent_tit = sprintf($linkpatt, get_permalink($post->post_parent), get_the_title($post->post_parent));
                            $_out = implode($sep, array($_crumbs, $parent_tit));
                            $out = $this->_add_title($_out, $post);
                        }
                    }
                } // single
                elseif (is_single()) {
                    if (!$out = apply_filters('post_tax_crumbs', '', $term, $this)) {
                        $_crumbs = $this->_tax_crumbs($term, 'self');
                        $out = $this->_add_title($_crumbs, $post);
                    }
                } // не древовидная такса (метки)
                elseif (!is_taxonomy_hierarchical($term->taxonomy)) {
                    // метка
                    if (is_tag())
                        $out = $this->_add_title('', $term, sprintf($loc->tag, esc_html($term->name)));
                    // такса
                    elseif (is_tax()) {
                        $post_label = $ptype->labels->name;
                        $tax_label = $GLOBALS['wp_taxonomies'][$term->taxonomy]->labels->name;
                        $out = $this->_add_title('', $term, sprintf($loc->tax_tag, $post_label, $tax_label, esc_html($term->name)));
                    }
                } // древовидная такса (рибрики)
                else {
                    if (!$out = apply_filters('term_tax_crumbs', '', $term, $this)) {
                        $_crumbs = $this->_tax_crumbs($term, 'parent');
                        $out = $this->_add_title($_crumbs, $term, esc_html($term->name));
                    }
                }
            } // влоежния от записи без терминов
            elseif (is_attachment()) {
                $parent = get_post($post->post_parent);
                $parent_link = sprintf($linkpatt, get_permalink($parent), esc_html($parent->post_title));
                $_out = $parent_link;

                // вложение от записи древовидного типа записи
                if (is_post_type_hierarchical($parent->post_type)) {
                    $parent_crumbs = $this->_page_crumbs($parent);
                    $_out = implode($sep, array($parent_crumbs, $parent_link));
                }

                $out = $this->_add_title($_out, $post);
            } // записи без терминов
            elseif (is_singular()) {
                $out = $this->_add_title('', $post);
            }
        }

        // замена ссылки на архивную страницу для типа записи
        $home_after = apply_filters('phonexa_breadcrumbs_home_after', '', $linkpatt, $sep, $ptype);

        if ('' === $home_after) {
            // Ссылка на архивную страницу типа записи для: отдельных страниц этого типа; архивов этого типа; таксономий связанных с этим типом.
            if ($ptype && $ptype->has_archive && !in_array($ptype->name, array('post', 'page', 'attachment'))
                && (is_post_type_archive() || is_singular() || (is_tax() && in_array($term->taxonomy, $ptype->taxonomies)))
            ) {
                $pt_title = $ptype->labels->name;

                // первая страница архива типа записи
                if (is_post_type_archive() && !$paged_num)
                    $home_after = sprintf($this->arg->title_patt, $pt_title);
                // singular, paged post_type_archive, tax
                else {
                    $home_after = sprintf($linkpatt, get_post_type_archive_link($ptype->name), $pt_title);

                    $home_after .= (($paged_num && !is_tax()) ? $pg_end : $sep); // пагинация
                }
            }
        }

        $before_out = sprintf($linkpatt, home_url(), $loc->home) . ($home_after ? $sep . $home_after : ($out ? $sep : ''));

        $out = apply_filters('phonexa_breadcrumbs_pre_out', $out, $sep, $loc, $arg);

        $out = sprintf($wrappatt, $before_out . $out);

        return apply_filters('phonexa_breadcrumbs', $out, $sep, $loc, $arg);
    }

    function _page_crumbs($post)
    {
        $parent = $post->post_parent;

        $crumbs = array();
        while ($parent) {
            $page = get_post($parent);
            $crumbs[] = sprintf($this->arg->linkpatt, get_permalink($page), esc_html($page->post_title));
            $parent = $page->post_parent;
        }

        return implode($this->arg->sep, array_reverse($crumbs));
    }

    function _tax_crumbs($term, $start_from = 'self')
    {
        $termlinks = array();
        $term_id = ($start_from === 'parent') ? $term->parent : $term->term_id;
        while ($term_id) {
            $term = get_term($term_id, $term->taxonomy);
            $termlinks[] = sprintf($this->arg->linkpatt, get_term_link($term), esc_html($term->name));
            $term_id = $term->parent;
        }

        if ($termlinks)
            return implode($this->arg->sep, array_reverse($termlinks)) /*. $this->arg->sep*/ ;
        return '';
    }

    // добалвяет заголовок к переданному тексту, с учетом всех опций. Добавляет разделитель в начало, если надо.
    function _add_title($add_to, $obj, $term_title = '')
    {
        $arg = &$this->arg; // упростим...
        $title = $term_title ? $term_title : esc_html($obj->post_title); // $term_title чиститься отдельно, теги моугт быть...
        $show_title = $term_title ? $arg->show_term_title : $arg->show_post_title;

        // пагинация
        if ($arg->pg_end) {
            $link = $term_title ? get_term_link($obj) : get_permalink($obj);
            $add_to .= ($add_to ? $arg->sep : '') . sprintf($arg->linkpatt, $link, $title) . $arg->pg_end;
        } // дополняем - ставим sep
        elseif ($add_to) {
            if ($show_title)
                $add_to .= $arg->sep . sprintf($arg->title_patt, $title);
            elseif ($arg->last_sep)
                $add_to .= $arg->sep;
        } // sep будет потом...
        elseif ($show_title)
            $add_to = sprintf($arg->title_patt, $title);

        return $add_to;
    }

}

/**
 * Изменения:
 * 3.3 - новые хуки: attachment_tax_crumbs, post_tax_crumbs, term_tax_crumbs. Позволяют дополнить крошки таксономий.
 * 3.2 - баг с разделителем, с отключенным 'show_term_title'. Стабилизировал логику.
 * 3.1 - баг с esc_html() для заголовка терминов - с тегами получалось криво...
 * 3.0 - Обернул в класс. Добавил опции: 'title_patt', 'last_sep'. Доработал код. Добавил пагинацию для постов.
 * 2.5 - ADD: Опция 'show_term_title'
 * 2.4 - Мелкие правки кода
 * 2.3 - ADD: Страница записей, когда для главной установлена отделенная страница.
 * 2.2 - ADD: Link to post type archive on taxonomies page
 * 2.1 - ADD: $sep, $loc, $args params to hooks
 * 2.0 - ADD: в фильтр 'phonexa_breadcrumbs_home_after' добавлен четвертый аргумент $ptype
 * 1.9 - ADD: фильтр 'phonexa_breadcrumbs_default_loc' для изменения локализации по умолчанию
 * 1.8 - FIX: заметки, когда в рубрике нет записей
 * 1.7 - Улучшена работа с приоритетными таксономиями.
 */
