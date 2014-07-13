<?php namespace Wright\Middleware;

class PaginateMiddleware
{
    public function __invoke($pages, $neighbors = 5, $number_per_page = 100)
    {
        $all_results = [];

        $number_of_links = $neighbors * 2 + 1;

        foreach ($pages as $permalink => $page) {

            $permalink = rtrim($permalink, '/');

            $link = function ($page_number) use ($permalink) {
                return $permalink . '/' . $page_number . '/';
            };

            $results = [];

            $page_number = 1;

            $offset = 0;

            do {

                $collection = $page['page']->collection($offset, $number_per_page);

                if ($collection) {

                    $last_page = $page_number;

                    $result = $page;

                    $pagination = [
                        'collection' => $collection
                    ];

                    $result['pagination'] = $pagination;

                    $results[$link($page_number)] = $result;

                    $offset += $number_per_page;

                    $page_number++;
                }

            } while (count($collection) == $number_per_page);

            while (--$page_number) {

                $pagination = $results[$link($page_number)]['pagination'];

                $links = [];

                $links[$page_number] = $link($page_number);

                $pagination['current'] = $link($page_number);

                $i = $page_number - 1;

                while ($i >= 1 && $i >= $page_number - $neighbors) {

                    $links[$i] = $link($i);

                    $i--;
                }

                $i = $page_number + 1;

                while ($i <= $last_page && count($links) < $number_of_links) {

                    $links[$i] = $link($i);

                    $i++;
                }

                $i = $page_number - 1;

                while ($i >= 1 && count($links) < $number_of_links) {

                    $links[$i] = $link($i);

                    $i--;
                }

                ksort($links);

                if (!isset($links[1])) {

                    reset($links);

                    unset($links[key($links)]);

                    $pagination['first'] = $link(1);
                }

                if (!isset($links[$last_page])) {

                    end($links);

                    unset($links[key($links)]);

                    $pagination['last'] = $link($last_page);
                }

                $pagination['links'] = $links;

                $results[$link($page_number)]['pagination'] = $pagination;
            }

            $results[$permalink . '/'] = $results[$link(1)];

            $all_results += $results;
        }

        return $all_results;
    }
}
