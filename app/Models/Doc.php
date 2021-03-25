<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use simplehtmldom\HtmlWeb;

class Doc extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function content(): HasOne
    {
        return $this->hasOne(Content::class);
    }

    public static function fetchNews($fromYear = null)
    {
        $fromYear = $fromYear ?? (now()->year - 1);
        foreach (range($fromYear, now()->year) as $year)
            self::fetchYear($year);
    }

    public static function fetchYear($year)
    {
        $client = new HtmlWeb();
        foreach (range(1,12) as $month) {
            $html = $client->load("https://nvd.nist.gov/vuln/full-listing/$year/$month");
            if (!is_null($html)) {
                $docs = $html->find('span[class="col-md-2"] > a');
                foreach ($docs as $url)
                    try {
                        $slug = str_replace('/vuln/detail/', '', $url->href);
                        self::create([
                            'year' => explode('-', $slug)[1],
                            'month' => $month,
                            'slug' => $slug,
                            'nvd_url' => 'https://nvd.nist.gov' . $url->href
                        ]);
                    } catch(\Exception $ex) {Log::error($ex);}
            }
            Log::info('- - - - - - Year = ' . $year . ' and Month = ' . $month . ' Completed.');
        }
    }

    public function fetchContent()
    {
        if (!is_null($this->content)) return;
        $client = new HtmlWeb();
        $html = $client->load($this->nvd_url);

        $fetchedContent['doc_id'] = $this->id;
        $fetchedContent['published_date'] = try_catch_null(function () use ($html) {
            return Carbon::createFromFormat('d/m/Y', $html->find('span[data-testid=vuln-published-on]')[0]->innertext)->format('Y-m-d');
        });
        $fetchedContent['modified_date'] = try_catch_null(function () use ($html) {
            return Carbon::createFromFormat('d/m/Y', $html->find('span[data-testid=vuln-last-modified-on]')[0]->innertext)->format('Y-m-d');
        });
        $fetchedContent['source'] = try_catch_null(function () use ($html) {
            return $html->find('span[data-testid=vuln-current-description-source]')[0]->innertext;
        });
        $fetchedContent['current_description'] = try_catch_null(function () use ($html) {
            return $html->find('p[data-testid=vuln-description]')[0]->innertext;
        });
        $fetchedContent['analysis_description'] = try_catch_null(function () use ($html) {
            return $html->find('p[data-testid=vuln-analysis-description]')[0]->innertext;
        });
        $fetchedContent['hyperlink'] = try_catch_null(function () use ($html) {
            return str_replace(['  ', 'Please address comments about this page to ', '<a href="mailto:nvd@nist.gov">nvd@nist.gov</a>', ' .'], '', $html->find('#vulnHyperlinksPanel > p')[0]->innertext);
        });
        $fetchedContent['hyperlink_table'] = try_catch_null(function () use ($html) {
            return $html->find('table[data-testid=vuln-hyperlinks-table]')[0]->outertext;
        });
        $fetchedContent['technical_table'] = try_catch_null(function () use ($html) {
            return $html->find('table[data-testid=vuln-CWEs-table]')[0]->outertext;
        });
        $fetchedContent['configurations_table'] = try_catch_null(function () use ($html) {
            return json_encode(['nonVulnerable' => '',
                'vulnerable' => ''
            ]);
        });
        $fetchedContent['change_history'] = try_catch_null(function () use ($html) {
            return $html->find('div[class=vuln-change-history-container]')[0]->outertext;
        });

        /** @var Content $content */
        $content = Content::create($fetchedContent);

        foreach ($html->find('#vulnCvssPanel > div[class=container-fluid]') as $section) {
            foreach ($section->find('div[class=row no-gutters]') as $row) {
                Score::create([
                    'content_id' => $content->id,
                    'title' => try_catch_null(function () use ($row) {
                        return strtolower(str_replace(':','', $row->find('div=[class=col-lg-9 col-sm-6]')[0]->firstChild()->innertext));
                    }),
                    'source' => try_catch_null(function () use ($row) {
                        return strtolower($row->find('div=[class=col-lg-9 col-sm-6]')[0]->lastChild()->innertext);
                    }),
                    'score' => try_catch_null(function () use ($row) {
                        return $row->find('span[class=severityDetail] > a')[0]->innertext == 'N/A' ? 'N/A' : strtolower(explode(' ', $row->find('span[class=severityDetail] > a')[0]->innertext)[0]);
                    }),
                    'score_desc' => try_catch_null(function () use ($row) {
                        return $row->find('span[class=severityDetail] > a')[0]->innertext == 'N/A' ? null : strtolower(explode(' ', $row->find('span[class=severityDetail] > a')[0]->innertext)[1]);
                    }),
                    'vector' => try_catch_null(function () use ($row) {
                        return $row->find('div[class="col-lg-6 col-sm-12"] > span > span')[0]->innertext;
                    }),
                    'version' => try_catch_null(function () use ($section) {
                        return explode(' ', $section->find('strong')[0]->innertext)[1];
                    }),
                    'icon' => try_catch_null(function () use ($row) {
                        return 'https://nvd.nist.gov' . $row->find('img[class="cvssNvdIcon"]')[0]->src;
                    })
                ]);
            }
        }

        return $content;
    }
}
